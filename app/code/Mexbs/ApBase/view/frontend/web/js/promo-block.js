define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data',
    'Mexbs_ApBase/js/model/promo-add'
], function (
    $,
    modal,
    customerData,
    promoAdd
    ) {
    'use strict';

    return function(options){
        $(document).ready(function(){
            var currentVisibleStep = 1;

            var reloadPromoProductsBlock = function(){
                $(".cart-promos-wrapper").html('');
                if('loaderImageUrl' in options){
                    $(".cart-promos-wrapper").html('<img alt="Loading..." src="'+ options.loaderImageUrl +'">');
                }
//                $(".cart-promos-wrapper-title").hide();
                var params = {};
                if(('productId' in options) && $.isNumeric(options.productId)){
                    params['product_id'] = options.productId;
                }
                if('location' in options){
                    params['location'] = options.location;
                }
                $.ajax({
                    url: options.promoProductsUrl,
                    type: 'post',
                    dataType: 'json',
                    data: params,
                    success: function (response) {
                        var promosHtml = '';
                        var promoHtml;
                        for(var promoHtmlIndex in response){
                            promoHtml = "";
                            if(response.hasOwnProperty(promoHtmlIndex)){
                                promoHtml = response[promoHtmlIndex];
                            }
                            if(promoHtml != ""){
                                promosHtml = promosHtml + promoHtml;
                            }
                        }
                        $(".cart-promos-wrapper").html(promosHtml);
                        if(promosHtml != ""){
                            $(".cart-promos-wrapper-title").show();
                        }

                        if(promosHtml.trim() != ''){
                            $(".cart-promos-wrapper-title").show();
                            initImagesSlider();
                        }
                    }
                });
            };

            if(options.shouldLoadThePromoBlock == true){
                reloadPromoProductsBlock();
            }

            var popupTpl = '<aside ' +
                'class="modal-<%= data.type %> <%= data.modalClass %> ' +
                '<% if(data.responsive){ %><%= data.responsiveClass %><% } %> ' +
                '<% if(data.innerScroll){ %><%= data.innerScrollClass %><% } %>"'+
                'data-role="modal"' +
                'data-type="<%= data.type %>"' +
                'tabindex="0">'+
                '    <div data-role="focusable-start" tabindex="0"></div>'+
                '    <div class="modal-inner-wrap"'+
                'data-role="focusable-scope">'+
                '    <div '+
                'class="modal-content" '+
                'data-role="content">' +
                '<button '+
                'class="action-close" '+
                'data-role="closeBtn" '+
                'type="button">'+
                '<span><%= data.closeText %></span>' +
                '</button>' +
                '</div>'+
                '   </div>'+
                '   </aside>';

            var modalOptions = {
                type: 'popup',
                responsive: false,
                innerScroll: false,
                buttons: [],
                popupTpl: popupTpl,
                modalClass: 'cart-promo-add-to-cart-modal-wrapper',
                closeText: 'X'
            };

            var initImagesSlider = function(){
                if($('#cart-promo-product-groups-images-slider').length > 0){
                    var slideCount = $('#cart-promo-product-groups-images-slider ul li').length;
                    window.slideWidth = $('#cart-promo-product-groups-images-slider ul li').width();
                    var slideHeight = $('#cart-promo-product-groups-images-slider ul li').height();
                    var sliderUlWidth = slideCount * slideWidth;

                    $('#cart-promo-product-groups-images-slider').css({ width: slideWidth, height: slideHeight });

                    $('#cart-promo-product-groups-images-slider ul').css({ width: sliderUlWidth });

                    $('#cart-promo-product-groups-images-slider ul li:last-child').prependTo('#cart-promo-product-groups-images-slider ul');
                }
            };


            function moveLeft(e){
                $('#cart-promo-product-groups-images-slider ul').animate({
                    left: '+=' + slideWidth + "px"
                }, 200, function () {
                    $('#cart-promo-product-groups-images-slider ul li:last-child').prependTo('#cart-promo-product-groups-images-slider ul');
                    $('#cart-promo-product-groups-images-slider ul').css('left', '');
                });
            }

            function moveRight(e) {
                $('#cart-promo-product-groups-images-slider ul').animate({
                    left: '-=' + slideWidth + "px"
                }, 200, function () {
                    $('#cart-promo-product-groups-images-slider ul li:first-child').appendTo('#cart-promo-product-groups-images-slider ul');
                    $('#cart-promo-product-groups-images-slider ul').css('left', '');
                });
            }

            $("body").on("click", "#cart-promo-product-groups-images-slider div.control_prev", function(e){
                moveLeft(e);
            }).on("click", "#cart-promo-product-groups-images-slider div.control_next", function(e){
                moveRight(e);
            });

            $(".cart-promos-wrapper").on("click", "button[data-action='promo-add-to-cart']", function(){
                var params = { rule_id: $(this).attr("data-promo-id")};
                if($.isNumeric(options.productId)){
                    params['product_id'] = options.productId;
                }
                if($(this).closest(".cart-promo-wrapper").attr("data-rule-has-selections") == 0
                    ){
                    var allConfigurationOptionsSelectedInAllParts = true;
                    var allConfigurationOptionsSelectedInPart = true;
                    var allCustomOptionsSelectedInAllParts = true;
                    var allCustomOptionsSelectedInPart = true;

                    $(this).closest(".cart-promo-wrapper").find(".cart-promo-product-group-wrapper").each(function(){
                        allConfigurationOptionsSelectedInPart = promoAdd.isAllConfigurationOptionsSelectedInPart($(this).attr("data-first-product-id"));
                        if(!allConfigurationOptionsSelectedInPart){
                            allConfigurationOptionsSelectedInAllParts = false;
                        }
                        allCustomOptionsSelectedInPart = promoAdd.isAllCustomOptionsSelectedInPart($(this).attr("data-first-product-id"));
                        if(!allCustomOptionsSelectedInPart){
                            allCustomOptionsSelectedInAllParts = false;
                        }
                    });
                    if(allConfigurationOptionsSelectedInAllParts && allCustomOptionsSelectedInAllParts){
                        promoAdd.performAddToCartRequest(options.promoAddToCartUrl, ".cart-promo-wrapper[data-rule-id='"+$(this).attr("data-promo-id")+"'] .cart-promo-product-group-wrapper", ".cart-promo-wrapper[data-rule-id='"+$(this).attr("data-promo-id")+"'] .cart-promo-product-group-wrapper", "data-first-product-id", ".cart-promo-product-group-qty span", true, reloadPromoProductsBlock);
                    }
                }else{
                    var productId = null;
                    if($.isNumeric(options.productId)){
                        productId = options.productId;
                    }
                    promoAdd.sendPromoAddToCartRequest(options.promoAddToCartHtmlUrl, params, '.cart-promo-add-to-cart-modal', modalOptions);
                }

            }).on("click", "button[data-action='promo-add-to-cart-part']", function(){
                    var productId = $(this).closest(".cart-promo-product-group-wrapper").attr("data-first-product-id");
                var allConfigurationOptionsSelectedInPart = promoAdd.isAllConfigurationOptionsSelectedInPart(productId);
                var allCustomOptionsSelectedInPart = promoAdd.isAllCustomOptionsSelectedInPart(productId);
                if(allConfigurationOptionsSelectedInPart && allCustomOptionsSelectedInPart){
                    promoAdd.performAddToCartPartRequest(options.promoAddToCartPartUrl, $(this).closest(".cart-promo-product-group-wrapper"), productId, true, reloadPromoProductsBlock);
                }
            });

            var hideStep = function(stepNumber){
                $('.cart-add-promo-group-wrapper[data-step-index="'+stepNumber+'"]').hide();
            };
            var showStep = function(stepNumber){
                $('.cart-add-promo-group-wrapper[data-step-index="'+stepNumber+'"]').show();
            };

            var selectionsPerStep = [];


            var getMissingProductsText = function(){
                var currentGroupSelected = 0;
                if(typeof selectionsPerStep[currentVisibleStep] != 'undefined'){
                    currentGroupSelected = promoAdd.getStepSelectionsQtyExclProduct(currentVisibleStep, 0, selectionsPerStep);
                }
                var currentGroupQty = $('.cart-add-promo-group-wrapper[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var addS = "s";
                if((currentGroupQty - currentGroupSelected) == 1){
                    addS = "";
                }
                return "Please select " + (currentGroupQty - currentGroupSelected) + " more product" + addS;

            };

            var updateProductQtyOfCurrentStep = function(productId){
                if(typeof selectionsPerStep[currentVisibleStep] == 'undefined'){
                    selectionsPerStep[currentVisibleStep] = [];
                }
                var currentGroupQty = $('.cart-add-promo-group-wrapper[data-step-index="' + currentVisibleStep + '"]').attr("data-group-qty");
                var qtyInput = $('.cart-add-promo-product-item-info[data-product-id="'+productId+'"]').find(".cart-add-promo-product-item-qty input");
                var checkbox = $('.cart-add-promo-product-item-info[data-product-id="'+productId+'"]').find(".cart-add-promo-product-checkbox-container input");

                var productQty = 1;
                if(qtyInput.length > 0
                    && $.isNumeric(qtyInput.val())){
                    productQty = qtyInput.val();
                }

                var qtyLeftToSelect = currentGroupQty - promoAdd.getStepSelectionsQtyExclProduct(currentVisibleStep, productId, selectionsPerStep);

                if(productQty > qtyLeftToSelect){
                    productQty = qtyLeftToSelect;
                }

                if(checkbox.is(":checked")
                    && (productQty > 0)){
                    selectionsPerStep[currentVisibleStep][productId] = productQty;
                    qtyInput.val(productQty);
                }else{
                    checkbox.prop("checked", false);
                    if(typeof selectionsPerStep[currentVisibleStep][productId] != 'undefined'){
                        selectionsPerStep[currentVisibleStep].splice(productId, 1);
                    }

                    qtyInput.val("");
                }
            };

            $("body").on("click", 'button[data-action="go-to-previous-step"]',
                function(){
                    hideStep(currentVisibleStep);
                    currentVisibleStep--;
                    showStep(currentVisibleStep);
                }).on("click", 'div.swatch-option',
                function(){
                    var checkboxContainer = $(this).closest(".cart-add-promo-product-item-info").find(".cart-add-promo-product-checkbox-container");

                    promoAdd.swatchOptionClickedHandle(this, checkboxContainer);
                }).on("click", '.cart-add-promo-product-checkbox-container',
                function(event){
                    if(!($(this).find('input').is(":checked"))){
                        $(this).find("input").prop("checked", true);
                    }else{
                        $(this).find("input").prop("checked", false);
                    }

                    var productId = $(this).closest('.cart-add-promo-product-item-info').attr("data-product-id");

                    updateProductQtyOfCurrentStep(productId);
                    promoAdd.updateCurrentStepSelectedText(currentVisibleStep, selectionsPerStep, '.cart-add-promo-group-wrapper', '.cart-add-promo-group-chosen');

                    event.stopPropagation();
                    event.preventDefault();
                }).on("click", '.cart-add-promo-wrapper-button-done',
                function(){
                    var allConfigurationOptionsSelected = promoAdd.isAllConfigurationOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var requiredNumberOfProductsSelected = promoAdd.isRequiredNumberOfProductsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper');
                    var allCustomOptionsSelected = promoAdd.isAllCustomOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info', '.cart-add-promo-product-custom-options-wrapper');
                    if(!allConfigurationOptionsSelected || !allCustomOptionsSelected){
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').show();
                    }else{
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').hide();
                    }
                    if(!requiredNumberOfProductsSelected){
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').text(getMissingProductsText()).show();
                    }else{
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').first().hide();
                    }
                    if(allConfigurationOptionsSelected && allCustomOptionsSelected && requiredNumberOfProductsSelected){
                        $(".cart-promo-add-to-cart-modal-wrapper button.action-close").trigger("click");
                        selectionsPerStep = [];
                        currentVisibleStep = 1;

                        promoAdd.performAddToCartRequest(options.promoAddToCartUrl, '.cart-add-promo-product-checkbox-container input:checked', '.cart-add-promo-product-item-info', 'data-product-id', '.cart-add-promo-product-item-qty input', true, reloadPromoProductsBlock);
                    }

                }).on("click", '.cart-add-promo-wrapper-button-next',
                function(){
                    var allConfigurationOptionsSelected = promoAdd.isAllConfigurationOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var allCustomOptionsSelected = promoAdd.isAllCustomOptionsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper', this, '.cart-add-promo-product-item-info');
                    var requiredNumberOfProductsSelected = promoAdd.isRequiredNumberOfProductsSelected(selectionsPerStep, currentVisibleStep,'.cart-add-promo-group-wrapper');
                    if(!allConfigurationOptionsSelected || !allCustomOptionsSelected){
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').show();
                    }else{
                        $('.cart-add-promo-wrapper-error-configurations[data-step-index="' + currentVisibleStep + '"]').hide();
                    }
                    if(!requiredNumberOfProductsSelected){
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').text(getMissingProductsText()).show();
                    }else{
                        $('.cart-add-promo-wrapper-error-products[data-step-index="' + currentVisibleStep + '"]').hide();
                    }

                    if(allConfigurationOptionsSelected
                        && requiredNumberOfProductsSelected
                        && allCustomOptionsSelected){
                        hideStep(currentVisibleStep);
                        currentVisibleStep++;
                        showStep(currentVisibleStep);
                    }
                })
                .on("change", ".cart-add-promo-product-item-qty input", function(){
                    var newProductQty = 0;
                    if($.isNumeric($(this).val())){
                        newProductQty = $(this).val();
                    }
                    var checkboxContainer = $(this).closest(".cart-add-promo-product-item-info").find(".cart-add-promo-product-checkbox-container");
                    var productId = $(this).closest(".cart-add-promo-product-item-info").attr("data-product-id");
                    if(newProductQty > 0){
                        if(!(checkboxContainer.find("input").is(":checked"))){
                            checkboxContainer.find("input").prop("checked", true);
                        }
                    }else{
                        if(checkboxContainer.find("input").is(":checked")){
                            checkboxContainer.find("input").prop("checked", false);
                        }
                    }
                    updateProductQtyOfCurrentStep(productId);
                    promoAdd.updateCurrentStepSelectedText(currentVisibleStep, selectionsPerStep, '.cart-add-promo-group-wrapper', '.cart-add-promo-group-chosen');
                });
        });
    };

});