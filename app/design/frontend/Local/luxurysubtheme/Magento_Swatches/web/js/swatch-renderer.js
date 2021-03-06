define(['jquery',
    'underscore',
    'mage/template',
    'mage/smart-keyboard-handler',
    'mage/translate',
    'priceUtils',
    'uiRegistry',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'mage/validation/validation'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils, registry) {
    'use strict';
    $.widget('mage.validation', $.mage.validation, {
        listenFormValidateHandler: function (event, validation) {
            var swatchWrapper, firstActive, swatches, swatch, successList, errorList, firstSwatch;
            this._superApply(arguments);
            swatchWrapper = '.swatch-attribute-options';
            swatches = $(event.target).find(swatchWrapper);
            if (!swatches.length) {
                return;
            }
            swatch = '.swatch-attribute';
            firstActive = $(validation.errorList[0].element || []);
            successList = validation.successList;
            errorList = validation.errorList;
            firstSwatch = $(firstActive).parent(swatch).find(swatchWrapper);
            keyboardHandler.focus(swatches);
            $.each(successList, function (index, item) {
                $(item).parent(swatch).find(swatchWrapper).attr('aria-invalid', false);
            });
            $.each(errorList, function (index, item) {
                $(item.element).parent(swatch).find(swatchWrapper).attr('aria-invalid', true);
            });
            if (firstSwatch.length) {
                $(firstSwatch).focus();
            }
        }
    });
    $.widget('mage.SwatchRendererTooltip', {
        options: {
            delay: 200,
            tooltipClass: 'swatch-option-tooltip'
        },
        _init: function () {
            var $widget = this,
                $this = this.element,
                $element = $('.' + $widget.options.tooltipClass),
                timer, type = parseInt($this.attr('option-type'), 10),
                label = $this.attr('option-label'),
                thumb = $this.attr('option-tooltip-thumb'),
                value = $this.attr('option-tooltip-value'),
                width = $this.attr('thumb-width'),
                height = $this.attr('thumb-height'),
                $image, $title, $corner;
            if (!$element.length) {
                $element = $('<div class="' +
                    $widget.options.tooltipClass + '"><div class="image"></div><div class="title"></div><div class="corner"></div></div>');
                $('body').append($element);
            }
            $image = $element.find('.image');
            $title = $element.find('.title');
            $corner = $element.find('.corner');
            $this.hover(function () {
                if (!$this.hasClass('disabled')) {
                    timer = setTimeout(function () {
                        var leftOpt = null,
                            leftCorner = 0,
                            left, $window;
                        if (type === 2) {
                            $image.css({
                                'background': 'url("' + thumb + '") no-repeat center',
                                'background-size': 'initial',
                                'width': width + 'px',
                                'height': height + 'px'
                            });
                            $image.show();
                        } else if (type === 1) {
                            $image.css({
                                background: value
                            });
                            $image.show();
                        } else if (type === 0 || type === 3) {
                            $image.hide();
                        }
                        $title.text(label);
                        leftOpt = $this.offset().left;
                        left = leftOpt + $this.width() / 2 - $element.width() / 2;
                        $window = $(window);
                        if (left < 0) {
                            left = 5;
                        } else if (left + $element.width() > $window.width()) {
                            left = $window.width() - $element.width() - 5;
                        }
                        leftCorner = 0;
                        if ($element.width() < $this.width()) {
                            leftCorner = $element.width() / 2 - 3;
                        } else {
                            leftCorner = (leftOpt > left ? leftOpt - left : left - leftOpt) + $this.width() / 2 - 6;
                        }
                        $corner.css({
                            left: leftCorner
                        });
                        $element.css({
                            left: left,
                            top: $this.offset().top - $element.height() - $corner.height() - 18
                        }).show();
                    }, $widget.options.delay);
                }
            }, function () {
                $element.hide();
                clearTimeout(timer);
            });
            $(document).on('tap', function () {
                $element.hide();
                clearTimeout(timer);
            });
            $this.on('tap', function (event) {
                event.stopPropagation();
            });
        }
    });
    $.widget('mage.SwatchRenderer', {
        options: {
            classes: {
                attributeClass: 'swatch-attribute',
                attributeLabelClass: 'swatch-attribute-label',
                attributeSelectedOptionLabelClass: 'swatch-attribute-selected-option',
                attributeOptionsWrapper: 'swatch-attribute-options',
                attributeInput: 'swatch-input',
                optionClass: 'swatch-option',
                selectClass: 'swatch-select',
                moreButton: 'swatch-more',
                loader: 'swatch-option-loading'
            },
            jsonConfig: {},
            jsonSwatchConfig: {},
            selectorProduct: '.product-info-main',
            selectorProductPrice: '[data-role=priceBox]',
            mediaGallerySelector: '[data-gallery-role=gallery-placeholder]',
            selectorProductTile: '.product-item',
            numberToShow: false,
            onlySwatches: false,
            enableControlLabel: true,
            controlLabelId: '',
            moreButtonText: $t('More'),
            mediaCallback: '',
            mediaCache: {},
            mediaGalleryInitial: [{}],
            useAjax: false,
            gallerySwitchStrategy: 'replace',
            inProductList: false,
            slyOldPriceSelector: '.sly-old-price',
            tierPriceTemplateSelector: '#tier-prices-template',
            tierPriceBlockSelector: '[data-role="tier-price-block"]',
            tierPriceTemplate: '',
            normalPriceLabelSelector: '.normal-price .price-label'
        },
        getProduct: function () {
            var products = this._CalcProducts();
            return _.isArray(products) ? products[0] : null;
        },
        _init: function () {
            if (_.isEmpty(this.options.jsonConfig.images)) {
                this.options.useAjax = true;
                this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
            }
            if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                this._sortAttributes();
                this._RenderControls();
                this._setPreSelectedGallery();
                $(this.element).trigger('swatch.initialized');
            } else {
                console.log('SwatchRenderer: No input data received');
            }
            this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();
            // if (this.checkIsInProductPage()) {

            if (this._determineProductData().isInProductView) {
                let selectedAttr = $.parseQuery();
                $.each(selectedAttr, function (key, value) {
                    if (value === "" || value === null) {
                        delete selectedAttr[key];
                    }
                });
                if (Object.keys(selectedAttr).length == 0) {
                    var that = this;
                    /*that._selected(that.element.find('.swatch-option.text').not('.disabled').first(), that, false, true);
                    that._selected(that.element.find('.swatch-option.color').not('.disabled').first(), that, false, true);*/
                    // console.log('one time');
                    var initSelX = setInterval(function () {
                        if($('[data-gallery-role=gallery-placeholder]', '.column.main').data('gallery')) {
                            that._SelectFirstFallback();
                            clearInterval(initSelX);
                        }
                    }, 400);
                }
                setTimeout(function () { $('.gallery-placeholder').css('visibility', 'visible'); }, 1500);
            }
        },
        _sortAttributes: function () {
            this.options.jsonConfig.attributes = _.sortBy(this.options.jsonConfig.attributes, function (attribute) {
                return parseInt(attribute.position, 10);
            });
        },
        _create: function () {
            var options = this.options,
                gallery = $('[data-gallery-role=gallery-placeholder]', '.column.main'),
                productData = this._determineProductData(),
                $main = productData.isInProductView ? this.element.parents('.column.main') : this.element.parents('.product-item-info');
            if (productData.isInProductView) {
                gallery.data('gallery') ? this._onGalleryLoaded(gallery) : gallery.on('gallery:loaded', this._onGalleryLoaded.bind(this, gallery));
            } else {
                options.mediaGalleryInitial = [{
                    'img': $main.find('.product-image-photo').attr('src')
                }];
            }
            this.productForm = this.element.parents(this.options.selectorProductTile).find('form:first');
            this.inProductList = this.productForm.length > 0;
        },
        _determineProductData: function () {
            var productId, isInProductView = false;
            productId = this.element.parents('.product-item-details').find('.price-box.price-final_price').attr('data-product-id');
            if (!productId) {
                productId = $('[name=product]').val();
                isInProductView = productId > 0;
            }
            return {
                productId: productId,
                isInProductView: isInProductView
            };
        },
        checkIsInProductPage: function () {
            return $('body').hasClass('catalog-product-view') ? true : false;
        },
        _RenderControls: function () {
            var $widget = this,
                container = this.element,
                classes = this.options.classes,
                chooseText = this.options.jsonConfig.chooseText;
            $widget.optionsMap = {};
            $.each(this.options.jsonConfig.attributes, function () {
                var item = this,
                    controlLabelId = 'option-label-' + item.code + '-' + item.id,
                    options = $widget._RenderSwatchOptions(item, controlLabelId),
                    select = $widget._RenderSwatchSelect(item, chooseText),
                    input = $widget._RenderFormInput(item),
                    listLabel = '',
                    label = '';
                if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                    return;
                }
                if ($widget.options.enableControlLabel) {
                    label += '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                        $('<i></i>').text(item.label).html() + '</span>' + '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                }
                if ($widget.inProductList) {
                    $widget.productForm.append(input);
                    input = '';
                    listLabel = 'aria-label="' + $('<i></i>').text(item.label).html() + '"';
                } else {
                    listLabel = 'aria-labelledby="' + controlLabelId + '"';
                }
                container.append('<div class="' + classes.attributeClass + ' ' + item.code + '" ' + 'attribute-code="' + item.code + '" ' + 'attribute-id="' + item.id + '">' +
                    label + '<div aria-activedescendant="" ' + 'tabindex="0" ' + 'aria-invalid="false" ' + 'aria-required="false" ' + 'role="listbox" ' + listLabel + 'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                    options + select + '</div>' + input + '</div>');
                $widget.optionsMap[item.id] = {};
                $.each(item.options, function () {
                    if (this.products.length > 0) {
                        $widget.optionsMap[item.id][this.id] = {
                            price: parseInt($widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount, 10),
                            products: this.products
                        };
                    }
                });
            });
            container.find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]').SwatchRendererTooltip();
            $('.' + classes.moreButton).nextAll().hide();
            $widget._EventListener();
            $widget._Rewind(container);
            $widget._EmulateSelected($.parseQuery());
            $widget._EmulateSelected($widget._getSelectedAttributes());
        },
        _RenderSwatchOptions: function (config, controlId) {
            var optionConfig = this.options.jsonSwatchConfig[config.id],
                optionClass = this.options.classes.optionClass,
                sizeConfig = this.options.jsonSwatchImageSizeConfig,
                moreLimit = parseInt(this.options.numberToShow, 10),
                moreClass = this.options.classes.moreButton,
                moreText = this.options.moreButtonText,
                countAttributes = 0,
                html = '';
            if (!this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                return '';
            }
            $.each(config.options, function (index) {
                var id, type, value, thumb, label, width, height, attr, swatchImageWidth, swatchImageHeight;
                if (!optionConfig.hasOwnProperty(this.id)) {
                    return '';
                }
                if (moreLimit === countAttributes++) {
                    html += '<a href="#" class="' + moreClass + '"><span>' + moreText + '</span></a>';
                }
                id = this.id;
                type = parseInt(optionConfig[id].type, 10);
                value = optionConfig[id].hasOwnProperty('value') ? $('<i></i>').text(optionConfig[id].value).html() : '';
                thumb = optionConfig[id].hasOwnProperty('thumb') ? optionConfig[id].thumb : '';
                width = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.width : 110;
                height = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.height : 90;
                label = this.label ? $('<i></i>').text(this.label).html() : '';
                attr = ' id="' + controlId + '-item-' + id + '"' + ' index="' + index + '"' + ' aria-checked="false"' + ' aria-describedby="' + controlId + '"' + ' tabindex="0"' + ' option-type="' + type + '"' + ' option-id="' + id + '"' + ' option-label="' + label + '"' + ' aria-label="' + label + '"' + ' option-tooltip-thumb="' + thumb + '"' + ' option-tooltip-value="' + value + '"' + ' role="option"' + ' thumb-width="' + width + '"' + ' thumb-height="' + height + '"';
                swatchImageWidth = _.has(sizeConfig, 'swatchImage') ? sizeConfig.swatchImage.width : 30;
                swatchImageHeight = _.has(sizeConfig, 'swatchImage') ? sizeConfig.swatchImage.height : 20;
                if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                    attr += ' option-empty="true"';
                }
                if (type === 0) {
                    html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) + '</div>';
                } else if (type === 1) {
                    html += '<div class="' + optionClass + ' color" ' + attr + ' style="background: ' + value + ' no-repeat center; background-size: initial;">' + '' + '</div>';
                } else if (type === 2) {
                    html += '<div class="' + optionClass + ' image" ' + attr + ' style="background: url(' + value + ') no-repeat center; background-size: initial;width:' +
                        swatchImageWidth + 'px; height:' + swatchImageHeight + 'px">' + '' + '</div>';
                } else if (type === 3) {
                    html += '<div class="' + optionClass + '" ' + attr + '></div>';
                } else {
                    html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
                }
            });
            return html;
        },
        _RenderSwatchSelect: function (config, chooseText) {
            var html;
            if (this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                return '';
            }
            html = '<select class="' + this.options.classes.selectClass + ' ' + config.code + '">' + '<option value="0" option-id="0">' + chooseText + '</option>';
            $.each(config.options, function () {
                var label = this.label,
                    attr = ' value="' + this.id + '" option-id="' + this.id + '"';
                if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                    attr += ' option-empty="true"';
                }
                html += '<option ' + attr + '>' + label + '</option>';
            });
            html += '</select>';
            return html;
        },
        _RenderFormInput: function (config) {
            return '<input class="' + this.options.classes.attributeInput + ' super-attribute-select" ' + 'name="super_attribute[' + config.id + ']" ' + 'type="text" ' + 'value="" ' + 'data-selector="super_attribute[' + config.id + ']" ' + 'data-validate="{required: false}" ' + 'aria-required="false" ' + 'aria-invalid="false">';
        },
        _EventListener: function () {
            var $widget = this,
                options = this.options.classes,
                target;
            $widget.element.on('click', '.' + options.optionClass, function () {
                return $widget._OnClick($(this), $widget);
            });
            $widget.element.on('change', '.' + options.selectClass, function () {
                return $widget._OnChange($(this), $widget);
            });
            $widget.element.on('click', '.' + options.moreButton, function (e) {
                e.preventDefault();
                return $widget._OnMoreClick($(this));
            });
            $widget.element.on('keydown', function (e) {
                if (e.which === 13) {
                    target = $(e.target);
                    if (target.is('.' + options.optionClass)) {
                        return $widget._OnClick(target, $widget);
                    } else if (target.is('.' + options.selectClass)) {
                        return $widget._OnChange(target, $widget);
                    } else if (target.is('.' + options.moreButton)) {
                        e.preventDefault();
                        return $widget._OnMoreClick(target);
                    }
                }
            });
        },
        _loadMedia: function () {
            var $main = this.inProductList ? this.element.parents('.product-item-info') : this.element.parents('.column.main'),
                images;
            if (this.options.useAjax) {
                this._debouncedLoadProductMedia();
            } else {
                // console.log(this.options.jsonConfig.images)
                images = this.options.jsonConfig.images[this.getProduct()];
                if (!images) {
                    images = this.options.mediaGalleryInitial;
                }
                this.updateBaseImage(this._sortImages(images), $main, !this.inProductList);
            }
        },
        _sortImages: function (images) {
            return _.sortBy(images, function (image) {
                return image.position;
            });
        },
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);
            if ($widget.inProductList) {
                $input = $widget.productForm.find('.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]');
            }

            if ($this.hasClass('disabled')) {
                return;
            }
            if ($this.hasClass('selected')) { } else {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }
            $widget._Rebuild();
            if ($this.hasClass('selected')) { } else {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }
            if ($widget.element.parents($widget.options.selectorProduct).find(this.options.selectorProductPrice).is(':data(mage-priceBox)')) {
                $widget._UpdatePrice();
            }
            $(document).trigger('updateMsrpPriceBlock', [parseInt($this.attr('index'), 10) + 1, $widget.options.jsonConfig.optionPrices]);
            $widget._loadMedia();
            $input.trigger('change');

        },
        _selected: function ($this, $widget, $rebuild = false, $onload = false) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);
            if ($widget.inProductList) {
                $input = $widget.productForm.find('.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]');
            }
            if ($this.hasClass('selected')) { } else {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }
            if ($rebuild) $widget._Rebuild();
            //LIN-480 This on click was breaking the catagory selection casuing infinit image loads
            if (!$("body").hasClass('catalog-category-view')) {
                if ($onload) $this.trigger('click');
            }
        },
        _getAttributeCodeById: function (attributeId) {
            var attribute = this.options.jsonConfig.mappedAttributes[attributeId];
            return attribute ? attribute.code : attributeId;
        },
        _toggleCheckedAttributes: function ($this, $wrapper) {
            $wrapper.attr('aria-activedescendant', $this.attr('id')).find('.' + this.options.classes.optionClass).attr('aria-checked', false);
            $this.attr('aria-checked', true);
        },
        _OnChange: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);
            if ($widget.productForm.length > 0) {
                $input = $widget.productForm.find('.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]');
            }
            if ($this.val() > 0) {
                $parent.attr('option-selected', $this.val());
                $input.val($this.val());
            } else {
                $parent.removeAttr('option-selected');
                $input.val('');
            }
            $widget._Rebuild();
            $widget._UpdatePrice();
            $widget._loadMedia();
            $input.trigger('change');
        },
        _OnMoreClick: function ($this) {
            $this.nextAll().show();
            $this.blur().remove();
        },
        _Rewind: function (controls) {
            controls.find('div[option-id], option[option-id]').removeClass('disabled').removeAttr('disabled');
            controls.find('div[option-empty], option[option-empty]').attr('disabled', true).addClass('disabled');
        },
        _Rebuild: function () {
            var $widget = this,
                controls = $widget.element.find('.' + $widget.options.classes.attributeClass + '[attribute-id]'),
                selected = controls.filter('[option-selected]');
            $widget._Rewind(controls);
            if (selected.length <= 0) {
                return;
            }

            $widget._ShowStockMessage();

            var enableIndex = [];
            controls.each(function () {
                var $this = $(this),
                    id = $this.attr('attribute-id'),
                    products = $widget._CalcProducts(id);
                if (selected.length === 1 && selected.first().attr('attribute-id') === id) {
                    return;
                }
                $this.find('[option-id]').each(function () {
                    var $element = $(this),
                        option = $element.attr('option-id');
                    if ($(this).parents('.swatch-attribute').index() == 0) {
                        return;
                    }
                    if (!$widget.optionsMap.hasOwnProperty(id) || !$widget.optionsMap[id].hasOwnProperty(option)) {
                        return;
                    }
                    if (_.intersection(products, $widget.optionsMap[id][option].products).length <= 0) {
                        $element.attr('disabled', true).addClass('disabled');
                    } else {
                        enableIndex.push($element.index());
                    }
                });
            });
            $widget._SelectFirstFallback();
        },
        _SelectFirstFallback: function() {
            if($('[data-gallery-role=gallery-placeholder]', '.column.main').data('gallery'))
            {
                var $widget = this, classes = $widget.options.classes;
                $widget.element.find('.' + classes.attributeOptionsWrapper).each(function() {
                    if($(this).find('.' + classes.optionClass + '.selected').length==0 || 
                        $(this).find('.' + classes.optionClass + '.selected.disabled').length>0) 
                        $(this).find('.' + classes.optionClass + ':not(.disabled):first').trigger('click');
                });
            }
        },
        _ShowStockMessage: function () {
            var $widget = this,
                controls = $widget.element.find('.' + $widget.options.classes.attributeClass + '[attribute-id]'),
                selected = controls.filter('[option-selected]');
            //Check weather product list page or view page
            if ($('body.catalog-product-view').size() >= 1) {
                if (controls.size() == selected.size()) {
                    var messageContainer = $('#stock-message');
                    var stockMessage = '';
                    var productQty = $widget.options.jsonConfig.quantities[this.getProduct()];
                    $widget.element.parents('.product-item-info').find('.classname').html(productQty);
                    // console.log("Stock", productQty)

                    if (productQty > 0 && productQty <= $widget.options.jsonConfig.stockTresholdLessThanN) {
                        stockMessage = $widget.options.jsonConfig.stockMessages.lessThanN;
                    }

                    if (productQty == $widget.options.jsonConfig.stockTresholdEqualsN) {
                        stockMessage = $widget.options.jsonConfig.stockMessages.equalsN;
                    }

                    messageContainer.html(stockMessage);

                }
            }
        },
        _CalcProducts: function ($skipAttributeId) {
            var $widget = this,
                products = [];
            $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                var id = $(this).attr('attribute-id'),
                    option = $(this).attr('option-selected');
                if ($skipAttributeId !== undefined && $skipAttributeId === id) {
                    return;
                }
                if (!$widget.optionsMap.hasOwnProperty(id) || !$widget.optionsMap[id].hasOwnProperty(option)) {
                    return;
                }
                if (products.length === 0) {
                    products = $widget.optionsMap[id][option].products;
                } else {
                    products = _.intersection(products, $widget.optionsMap[id][option].products);
                }
            });
            return products;
        },
        _UpdatePrice: function () {
            var $widget = this,
                $product = $widget.element.parents($widget.options.selectorProduct),
                $productPrice = $product.find(this.options.selectorProductPrice),
                options = _.object(_.keys($widget.optionsMap), {}),
                result, tierPriceHtml, isShow;
            $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                var attributeId = $(this).attr('attribute-id');
                options[attributeId] = $(this).attr('option-selected');
            });
            result = $widget.options.jsonConfig.optionPrices[_.findKey($widget.options.jsonConfig.index, options)];
            $productPrice.trigger('updatePrice', {
                'prices': $widget._getPrices(result, $productPrice.priceBox('option').prices)
            });
            isShow = typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount;
            $product.find(this.options.slyOldPriceSelector)[isShow ? 'show' : 'hide']();
            if (typeof result != 'undefined' && result.tierPrices.length) {
                if (this.options.tierPriceTemplate) {
                    tierPriceHtml = mageTemplate(this.options.tierPriceTemplate, {
                        'tierPrices': result.tierPrices,
                        '$t': $t,
                        'currencyFormat': this.options.jsonConfig.currencyFormat,
                        'priceUtils': priceUtils
                    });
                    $(this.options.tierPriceBlockSelector).html(tierPriceHtml).show();
                }
            } else {
                $(this.options.tierPriceBlockSelector).hide();
            }
            $(this.options.normalPriceLabelSelector).hide();
            _.each($('.' + this.options.classes.attributeOptionsWrapper), function (attribute) {
                if ($(attribute).find('.' + this.options.classes.optionClass + '.selected').length === 0) {
                    if ($(attribute).find('.' + this.options.classes.selectClass).length > 0) {
                        _.each($(attribute).find('.' + this.options.classes.selectClass), function (dropdown) {
                            if ($(dropdown).val() === '0') {
                                $(this.options.normalPriceLabelSelector).show();
                            }
                        }.bind(this));
                    } else {
                        $(this.options.normalPriceLabelSelector).show();
                    }
                }
            }.bind(this));
        },
        _getPrices: function (newPrices, displayPrices) {
            var $widget = this,
                optionPriceDiff = 0,
                allowedProduct, optionPrices, basePrice, optionFinalPrice;
            if (_.isEmpty(newPrices)) {
                allowedProduct = this._getAllowedProductWithMinPrice(this._CalcProducts());
                optionPrices = this.options.jsonConfig.optionPrices;
                basePrice = parseFloat(this.options.jsonConfig.prices.basePrice.amount);
                if (!_.isEmpty(allowedProduct)) {
                    optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);
                    optionPriceDiff = optionFinalPrice - basePrice;
                }
                if (optionPriceDiff !== 0) {
                    newPrices = this.options.jsonConfig.optionPrices[allowedProduct];
                } else {
                    newPrices = $widget.options.jsonConfig.prices;
                }
            }
            _.each(displayPrices, function (price, code) {
                if (newPrices[code]) {
                    displayPrices[code].amount = newPrices[code].amount - displayPrices[code].amount;
                }
            });
            return displayPrices;
        },
        _getAllowedProductWithMinPrice: function (allowedProducts) {
            var optionPrices = this.options.jsonConfig.optionPrices,
                product = {},
                optionFinalPrice, optionMinPrice;
            _.each(allowedProducts, function (allowedProduct) {
                optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);
                if (_.isEmpty(product) || optionFinalPrice < optionMinPrice) {
                    optionMinPrice = optionFinalPrice;
                    product = allowedProduct;
                }
            }, this);
            return product;
        },
        _LoadProductMedia: function () {
            var $widget = this,
                $this = $widget.element,
                productData = this._determineProductData(),
                mediaCallData, mediaCacheKey, mediaSuccessCallback = function (data) {
                    if (!(mediaCacheKey in $widget.options.mediaCache)) {
                        $widget.options.mediaCache[mediaCacheKey] = data;
                    }
                    $widget._ProductMediaCallback($this, data, productData.isInProductView);
                    setTimeout(function () {
                        $widget._DisableProductMediaLoader($this);
                    }, 300);
                };
            if (!$widget.options.mediaCallback) {
                return;
            }
            mediaCallData = {
                'product_id': this.getProduct()
            };
            mediaCacheKey = JSON.stringify(mediaCallData);
            if (mediaCacheKey in $widget.options.mediaCache) {
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                mediaSuccessCallback($widget.options.mediaCache[mediaCacheKey]);
            } else {
                mediaCallData.isAjax = true;
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                $widget.xhr = $.ajax({
                    url: $widget.options.mediaCallback,
                    cache: true,
                    type: 'GET',
                    dataType: 'json',
                    data: mediaCallData,
                    success: mediaSuccessCallback
                }).done(function () {
                    $widget._XhrKiller();
                });
            }
        },
        _EnableProductMediaLoader: function ($this) {
            var $widget = this;
            if ($('body.catalog-product-view').length > 0) {
                $this.parents('.column.main').find('.photo.image').addClass($widget.options.classes.loader);
            } else {
                $this.parents('.product-item-info').find('.product-image-photo').addClass($widget.options.classes.loader);
            }
        },
        _DisableProductMediaLoader: function ($this) {
            var $widget = this;
            if ($('body.catalog-product-view').length > 0) {
                $this.parents('.column.main').find('.photo.image').removeClass($widget.options.classes.loader);
            } else {
                $this.parents('.product-item-info').find('.product-image-photo').removeClass($widget.options.classes.loader);
            }
        },
        _ProductMediaCallback: function ($this, response, isInProductView) {
            var $main = isInProductView ? $this.parents('.column.main') : $this.parents('.product-item-info'),
                $widget = this,
                images = [],
                support = function (e) {
                    return e.hasOwnProperty('large') && e.hasOwnProperty('medium') && e.hasOwnProperty('small');
                };
            if (_.size($widget) < 1 || !support(response)) {
                this.updateBaseImage(this.options.mediaGalleryInitial, $main, isInProductView);
                return;
            }
            images.push({
                full: response.large,
                img: response.medium,
                thumb: response.small,
                isMain: true
            });
            if (response.hasOwnProperty('gallery')) {
                $.each(response.gallery, function () {
                    if (!support(this) || response.large === this.large) {
                        return;
                    }
                    images.push({
                        full: this.large,
                        img: this.medium,
                        thumb: this.small
                    });
                });
            }
            this.updateBaseImage(images, $main, isInProductView);
        },
        _setImageType: function (images) {
            var initial = this.options.mediaGalleryInitial[0].img;
            if (images[0].img === initial) {
                images = $.extend(true, [], this.options.mediaGalleryInitial);
            } else {
                images.map(function (img) {
                    if (!img.type) {
                        img.type = 'image';
                    }
                });
            }
            return images;
        },
        updateBaseImage: function (images, context, isInProductView) {
            var justAnImage = images[0],
                initialImages = this.options.mediaGalleryInitial,
                imagesToUpdate, gallery = context.find(this.options.mediaGallerySelector).data('gallery'),
                isInitial;
            if (isInProductView) {
                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                isInitial = _.isEqual(imagesToUpdate, initialImages);
                if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                    imagesToUpdate = imagesToUpdate.concat(initialImages);
                }
                imagesToUpdate = this._setImageIndex(imagesToUpdate);
                //LIN-480 Add a null check
                if (!_.isUndefined(gallery) && gallery) {
                    gallery.updateData(imagesToUpdate);

                } else {
                    context.find(this.options.mediaGallerySelector).on('gallery:loaded', function (loadedGallery) {
                        loadedGallery = context.find(this.options.mediaGallerySelector).data('gallery');
                        loadedGallery.updateData(imagesToUpdate);
                    }.bind(this));
                }
                if (isInitial) {
                    if ($.isFunction($(this.options.mediaGallerySelector).AddFotoramaVideoEvents)) {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                    }
                    /* $(this.options.mediaGallerySelector).AddFotoramaVideoEvents(); */
                } else {
                    if ($.isFunction($(this.options.mediaGallerySelector).AddFotoramaVideoEvents)) {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                            selectedOption: this.getProduct(),
                            dataMergeStrategy: this.options.gallerySwitchStrategy
                        });
                    }
                }
                //LIN-480 Add image switching
                if (justAnImage && justAnImage.img) {
                    $(context.context).closest('.product-item').find('.product-image-photo').attr('src', justAnImage.img);
                }
            } else if (justAnImage && justAnImage.img) {
                context.find('.product-image-photo').attr('src', justAnImage.img);
            }
            $('[data-gallery-role=gallery-placeholder]').on('gallery:loaded', function () {
                $(this).on('fotorama:ready', function () {
                    setTimeout(function () {
                        $('#product-options-wrapper .field:visible').each(function (el) {
                            var optionId = $(this).find('.mageworx-swatch-option').attr('data-option-id');
                            var select = $(this).find('#select_' + optionId);
                            $(select).trigger('change');
                        });
                    }, 1000);
                });
            });
        },
        _setImageIndex: function (images) {
            var length = images.length,
                i;
            for (i = 0; length > i; i++) {
                images[i].i = i + 1;
            }
            return images;
        },
        _XhrKiller: function () {
            var $widget = this;
            if ($widget.xhr !== undefined && $widget.xhr !== null) {
                $widget.xhr.abort();
                $widget.xhr = null;
            }
        },
        _EmulateSelected: function (selectedAttributes) {
            $.each(selectedAttributes, $.proxy(function (attributeCode, optionId) {

                if (attributeCode == 'config') return;
                var elem = this.element.find('.' + this.options.classes.attributeClass + '[attribute-code="' + attributeCode + '"]'),
                    parentInput = elem.parent();
                if (elem.hasClass('selected')) {
                    return;
                }
                if (parentInput.hasClass(this.options.classes.selectClass)) {
                    parentInput.val(optionId);
                    // console.log('trigger change');
                    parentInput.trigger('change');
                } else {
                    // console.log('trigger click');
                    elem.find('.swatch-option[option-id="' + optionId + '"]').trigger('click');
                }
            }, this));
        },
        _EmulateSelectedByAttributeId: function (selectedAttributes) {
            $.each(selectedAttributes, $.proxy(function (attributeId, optionId) {
                var elem = this.element.find('.' + this.options.classes.attributeClass + '[attribute-id="' + attributeId + '"] [option-id="' + optionId + '"]'),
                    parentInput = elem.parent();
                if (elem.hasClass('selected')) {
                    return;
                }
                if (parentInput.hasClass(this.options.classes.selectClass)) {
                    parentInput.val(optionId);
                    parentInput.trigger('change');
                } else {
                    elem.trigger('click');
                }
            }, this));

        },
        _getSelectedAttributes: function () {
            var hashIndex = window.location.href.indexOf('#'),
                selectedAttributes = {},
                params;
            if (hashIndex !== -1) {
                params = $.parseQuery(window.location.href.substr(hashIndex + 1));
                selectedAttributes = _.invert(_.mapObject(_.invert(params), function (attributeId) {
                    var attribute = this.options.jsonConfig.mappedAttributes[attributeId];
                    return attribute ? attribute.code : attributeId;
                }.bind(this)));
            }
            return selectedAttributes;
        },
        _onGalleryLoaded: function (element) {
            var galleryObject = element.data('gallery');
            this.options.mediaGalleryInitial = galleryObject.returnCurrentImages();
        },
        _setPreSelectedGallery: function () {
            var mediaCallData;
            if (this.options.jsonConfig.preSelectedGallery) {
                mediaCallData = {
                    'product_id': this.getProduct()
                };
                this.options.mediaCache[JSON.stringify(mediaCallData)] = this.options.jsonConfig.preSelectedGallery;
            }
        },
        getUrlParameter: function (sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        }
    });
    return $.mage.SwatchRenderer;
});
