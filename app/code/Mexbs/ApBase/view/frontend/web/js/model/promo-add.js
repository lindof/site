define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Customer/js/customer-data'
], function (
    $,
    modal,
    cartCache,
    customerData
    ) {
    'use strict';

    return {
        sendPromoAddToCartRequest: function (ajaxUrl, params, modalSelector, modalOptions) {
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                data: params,
                showLoader: true,
                dataType: 'json',
                success: function (response) {
                    if(!$.isEmptyObject(response)){
                        if(response.added == 'false' && response.hasOwnProperty('html')){
                            modal(modalOptions, $(modalSelector));
                            $(modalSelector).modal('openModal').html(response.html).trigger('contentUpdated');
                        }
                    }
                }
            });
        },
        isAllConfigurationOptionsSelected: function(selectionsPerStep, currentVisibleStep, promoWrapperSelector, that, productItemInfoSelector){
            var allConfigurationsHaveSelectedOption = true;
            try{
                for(var productId in selectionsPerStep[currentVisibleStep]){
                    $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"] ' + productItemInfoSelector +'[data-product-id="' + productId + '"] .swatch-attribute')
                        .each(
                        function(){
                            if($(this).find("select.swatch-select").length > 0){
                                if($(this).find("select.swatch-select").val() == 0){
                                    allConfigurationsHaveSelectedOption = false;
                                }
                            }else{
                                if($(this).find(".swatch-option.selected").length == 0){
                                    allConfigurationsHaveSelectedOption = false;
                                }
                            }
                        });
                }
            }catch(err) {}
            return allConfigurationsHaveSelectedOption;
        },
        isAllConfigurationOptionsSelectedInPart: function(productId){
            var allConfigurationsHaveSelectedOption = true;
            try{
                $('.cart-promo-product-group-wrapper[data-first-product-id="' + productId + '"] .swatch-attribute')
                    .each(
                    function(){
                        var currentErrorMessage = $(this).closest(".cart-promo-product-group-wrapper").find("[data-error-attribute-id="+$(this).attr('attribute-id')+"]");
                        if($(this).find("select.swatch-select").length > 0){
                            if($(this).find("select.swatch-select").val() == 0){
                                allConfigurationsHaveSelectedOption = false;
                            }
                        }else{
                            if($(this).find(".swatch-option.selected").length == 0){
                                allConfigurationsHaveSelectedOption = false;
                            }
                        }

                        if(!allConfigurationsHaveSelectedOption){
                            if(!currentErrorMessage.length){
                                $(this).after("<div class='error missing-configuration' data-error-attribute-id="+$(this).attr('attribute-id')+">Please select</div>");
                            }
                        }else{
                            if(currentErrorMessage.length){
                                currentErrorMessage.remove();
                            }
                        }
                    });
            }catch(err) {}
            return allConfigurationsHaveSelectedOption;
        },
        getStepSelectionsQtyExclProduct: function(stepNumber, exclProductId, selectionsPerStep){
            var stepSelectedQty = 0;

            if(typeof selectionsPerStep[stepNumber] != 'undefined'){
                for (var productId in selectionsPerStep[stepNumber]) {
                    if((exclProductId == 0) || (productId != exclProductId)){
                        if (selectionsPerStep[stepNumber].hasOwnProperty(productId)) {
                            stepSelectedQty += parseInt(selectionsPerStep[stepNumber][productId]);
                        }
                    }
                }
            }

            return stepSelectedQty;
        },
        isRequiredNumberOfProductsSelected: function(selectionsPerStep, currentVisibleStep, promoWrapperSelector){
            try{
                var requiredNumberOfProductsForStep = $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var stepSelectionsLength = 0;
                try{
                    stepSelectionsLength = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }catch(error){}

                if(stepSelectionsLength < requiredNumberOfProductsForStep){
                    return false;
                }
            }catch(err) {}
            return true;
        },
        isAtLeastOneProductSelected: function(selectionsPerStep, currentVisibleStep){
            try{
                var stepSelectionsLength = 0;
                try{
                    stepSelectionsLength = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }catch(error){}

                if(stepSelectionsLength < 1){
                    return false;
                }
            }catch(err) {}
            return true;
        },
        isAllCustomOptionsSelected : function(selectionsPerStep, currentVisibleStep, promoWrapperSelector, that, productItemInfoSelector){
            var allCustomOptionsConfigured = true;
            try{
                for(var productId in selectionsPerStep[currentVisibleStep]){
                    $(promoWrapperSelector + '[data-step-index="' + currentVisibleStep + '"] '+ productItemInfoSelector +'[data-product-id="' + productId + '"] div.field.required select').each(function(){
                        if($(this).find("option:selected").val() == ''){
                            $(this).addClass("mage-error");
                            allCustomOptionsConfigured = false;
                        }else{
                            $(this).removeClass("mage-error");
                        }
                    });

                    $(promoWrapperSelector +'[data-step-index="' + currentVisibleStep + '"] '+ productItemInfoSelector +'[data-product-id="' + productId + '"] div.field.required input').each(function(){
                        if($(this).val() == ''){
                            $(this).addClass("mage-error");
                            allCustomOptionsConfigured = false;
                        }else{
                            $(this).removeClass("mage-error");
                        }
                    });
                }
            }catch(err) {}
            return allCustomOptionsConfigured;
        },
        isAllCustomOptionsSelectedInPart : function(productId){
            var allCustomOptionsSelectedInPart = true;
            try{
                $('.cart-promo-product-group-wrapper[data-first-product-id="' + productId + '"] div.field.required select')
                    .each(
                    function(){
                        var currentErrorMessage = $(this).closest(".cart-promo-product-group-wrapper").find("[data-error-attribute-id="+$(this).attr('id')+"]");

                        if($(this).find("option:selected").val() == ''){
                            allCustomOptionsSelectedInPart = false;
                            if(!currentErrorMessage.length){
                                $(this).after("<div class='error missing-configuration' data-error-attribute-id="+$(this).attr('id')+">Please select</div>");
                            }
                        }else{
                            if(currentErrorMessage.length){
                                currentErrorMessage.remove();
                            }
                        }
                    });

                $('.cart-promo-product-group-wrapper[data-first-product-id="' + productId + '"] div.field.required input')
                    .each(
                    function(){
                        var currentErrorMessage = $(this).closest(".cart-promo-product-group-wrapper").find("[data-error-attribute-id="+$(this).attr('id')+"]");

                        if($(this).val() == ''){
                            allCustomOptionsSelectedInPart = false;
                            if(!currentErrorMessage.length){
                                $(this).after("<div class='error missing-configuration' data-error-attribute-id="+$(this).attr('id')+">Please select</div>");
                            }
                        }else{
                            if(currentErrorMessage.length){
                                currentErrorMessage.remove();
                            }
                        }
                    });
            }catch(err) {}
            return allCustomOptionsSelectedInPart;
        },
        getSpecificProductAddData: function(productContainer, productId, productQty){
            var bundleOption = {};
            var bundleOptionQty = {};

            var productOptions = {};

            var matches = [];
            var optionId = null;
            var subOptionId;

            var productOptionsLength = 0;
            productContainer.find(".swatch-attribute").each(function(){
                productOptions[productOptionsLength] = {};
                productOptions[productOptionsLength]["attribute_id"] = $(this).attr("attribute-id");

                if($(this).find("select.swatch-select").length > 0){
                    productOptions[productOptionsLength]["option_id"] = $(this).find("select.swatch-select option:selected").attr("option-id");
                }else{
                    productOptions[productOptionsLength]["option_id"] = $(this).find(".swatch-option.selected").attr("option-id");
                }

                productOptionsLength++;
            });

            productContainer.find(".product-custom-option").each(function(){
                if($(this).is(":radio")
                    || $(this).is(":checkbox") ){
                    if(!$(this).is(':checked')){
                        return;
                    }
                }
                matches = $(this).attr('name').match(/options\[(.*?)\](\[(.*?)\])?/);
                if(matches.length > 1){
                    subOptionId = null;
                    optionId = matches[1];

                    if(matches.length > 3){
                        subOptionId =  matches[3];
                    }


                    if( $(this).is(":checkbox") ){
                        if(!(optionId in productOptions)){
                            productOptions[optionId] = [];
                        }
                        productOptions[optionId].push($(this).val());
                    }else{
                        if(!(optionId in productOptions)){
                            productOptions[optionId] = {};
                        }
                        if((subOptionId != null)
                            && (subOptionId != "")){
                            productOptions[optionId][subOptionId] = $(this).val();
                        }else{
                            productOptions[optionId] = $(this).val();
                        }
                    }
                }
            });

            productContainer.find(".options-list input.bundle.option:checked").each(function(){
                matches = $(this).attr('name').match(/bundle_option\[(.*?)\]/);
                if(matches.length > 1){
                    optionId = matches[1];
                    if($(this).attr("type") == "checkbox"){
                        if(!(optionId in bundleOption)){
                            bundleOption[optionId] = [];
                        }
                        bundleOption[optionId].push($(this).val());
                    }else{
                        bundleOption[optionId] = $(this).val();
                    }
                }
            });

            productContainer.find("select.bundle.option option:selected").each(function(){
                matches = $(this).parent().attr('name').match(/bundle_option\[(.*?)\]/);
                if(matches.length > 1){
                    optionId = matches[1];
                    if(!(optionId in bundleOption)){
                        bundleOption[optionId] = [];
                    }
                    bundleOption[optionId].push($(this).val());
                }
            });

            productContainer.find(".options-list input[type='hidden'].bundle.option").each(function(){
                matches = $(this).attr('name').match(/bundle_option\[(.*?)\]/);
                if(matches.length > 1){
                    optionId = matches[1];
                    bundleOption[optionId] = $(this).val();
                }
            });

            productContainer.find(".options-list input.qty").each(function(){
                matches = $(this).attr('name').match(/bundle_option_qty\[(.*?)\]/);
                if(matches.length > 1){
                    optionId = matches[1];
                    bundleOptionQty[optionId] = $(this).val();
                }
            });

            return {
                'product_id' : productId,
                'qty' : productQty,
                'options' : productOptions,
                'bundle_option' : bundleOption,
                'bundle_option_qty' : bundleOptionQty
            };
        },
        getJSONProductsAddData: function(selectedProductContainerSelector, selectedProductContainerWrapperSelector, productIdAttribute, itemQtySelector){
            var productsAddData = [];
            var productAddData;
            var productQty = 1;
            var productQtyInputValue;

            var that = this;

            $(selectedProductContainerSelector).each(function(){
                productQty = 1;
                if(itemQtySelector != null){
                    productQtyInputValue = $(this).closest(selectedProductContainerWrapperSelector).find(itemQtySelector).val();
                    if(productQtyInputValue == ""){
                        productQtyInputValue = $(this).closest(selectedProductContainerWrapperSelector).find(itemQtySelector).text();
                    }

                    if($.isNumeric(productQtyInputValue)
                        && productQtyInputValue > 0){
                        productQty = productQtyInputValue;
                    }
                }
                productAddData = that.getSpecificProductAddData($(this).closest(selectedProductContainerWrapperSelector), $(this).closest(selectedProductContainerWrapperSelector).attr(productIdAttribute), productQty);
                productsAddData.push(productAddData);
            });
            return JSON.stringify(productsAddData);
        },
        swatchOptionClickedHandle: function(that, checkboxContainer){
            if($(that).hasClass("selected")
                && !(checkboxContainer.find("input").is(":checked"))){
                checkboxContainer.trigger("click");
            }
            if(this.isAllConfigurationOptionsSelected()){
                $(".cart-add-promo-wrapper-error-configurations").hide();
            }
        },
        performAddToCartRequest: function(ajaxUrl, promoCheckboxContainerSelector, selectedProductContainerWrapperSelector, productIdAttribute, promoItemQtySelector, toRunExtraCallbackFunction, extraCallbackFunction, currentGiftTriggerItemsOfSameGroup){
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                dataType: 'json',
                showLoader: true,
                data: {
                    products_add_data: this.getJSONProductsAddData(promoCheckboxContainerSelector, selectedProductContainerWrapperSelector, productIdAttribute, promoItemQtySelector),
                    gift_trigger_item_ids_qtys_of_same_group: currentGiftTriggerItemsOfSameGroup
                },
                success: function (response) {
                    if(response.hasOwnProperty('status')
                        && (response['status'] == 'success')){
                        if(response.hasOwnProperty('cart_html')){
                            $("form.form.form-cart").replaceWith($(response['cart_html']).find("form.form.form-cart"));
                            $("form.form.form-cart").trigger('contentUpdated');
                            $("<div class='message-success success message ap-promo-add-success'>The items have been added successfully!</div>").appendTo($(".cart-promos-messages"));
                        }
                    }

                    cartCache.clear('totals');
//                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                    customerData.reload(['cart'], false);
                    if(toRunExtraCallbackFunction){
                        extraCallbackFunction();
                    }
                    try {
                        reLoadHints();
                    }
                    catch(err) {}
                }
            });
        },
        performAddToCartPartRequest: function(ajaxUrl, productContainerSelector, productId, toRunExtraCallbackFunction, extraCallbackFunction){
            $.ajax({
                url: ajaxUrl,
                type: 'post',
                dataType: 'json',
                showLoader: true,
                data: {
                    product_add_data: JSON.stringify(this.getSpecificProductAddData($(productContainerSelector), productId, 1)),
                    product_id: productId
                },
                success: function (response) {
                    if(response.hasOwnProperty('status')
                        && (response['status'] == 'success')){
                        if(response.hasOwnProperty('cart_html')){
                            $("form.form.form-cart").replaceWith($(response['cart_html']).find("form.form.form-cart"));
                            $("form.form.form-cart").trigger('contentUpdated');
                        }
                        $("<div class='message-success success message ap-promo-add-success'>The items have been added successfully!</div>").appendTo($(".cart-promos-messages"));
                    }

                    cartCache.clear('totals');
//                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                    customerData.reload(['cart'], false);
                    if(toRunExtraCallbackFunction){
                        extraCallbackFunction();
                    }
                    try {
                        reLoadHints();
                    }
                    catch(err) {}
                }
            });
        },

        updateCurrentStepSelectedText: function(currentVisibleStep, selectionsPerStep, currentGroupDivSelector, promoChosenSelector){
            var currentStepSelectedQty = this.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
            var currentGroupDiv = $(currentGroupDivSelector + '[data-step-index="' + currentVisibleStep + '"]');
            var currentGroupQty = currentGroupDiv.attr("data-group-qty");
            var selectionStepText = "Selected " + currentStepSelectedQty + " out of " + currentGroupQty;

            currentGroupDiv.find(promoChosenSelector).text(selectionStepText);
        }
    };
});
