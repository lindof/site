/**
 * Cminds Marketplace checkout cart shipping mixin js component.
 *
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Cminds Team <info@cminds.com>
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Cminds_Marketplace/js/model/shipping-methods',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function (
    $,
    ko,
    quote,
    shippingMethods,
    priceUtils
) {
    'use strict';

    return function (originalComponent) {
        return originalComponent.extend({
            defaults: {
                template: 'Cminds_Marketplace/shipping-methods'
            },
            vendorsProducts: shippingMethods.getViewModel(),
            shippingMethodsEnabled: window.checkoutConfig.shippingMethodsEnabled,
            nonSupplierShippingPrice: window.checkoutConfig.nonSupplierShippingPrice,
            canDisplaySupplierShippingMethods: window.checkoutConfig.shippingMethodsEnabled && window.checkoutConfig.shippingMethodsExist,
            selectedShippingMethod: ko.computed(function () {
                    return quote.shippingMethod() ?
                        quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                        null;
                }
            ),

            initialize: function () {
                $(document).on('change', ".form-shipping-address input, [name='country_id'], [name='region_id']", function () {
                    var countryId = $('[name="country_id"] > option:selected').val();
                    var region = $('[name="region_id"] > option:selected').val();
                    var postcode = $('[name="postcode"]').val();
                    if (countryId && region && postcode) {
                        var sa = quote.shippingAddress();
                        sa.countryId = countryId;
                        sa.regionId = region;
                        sa.postcode = postcode;
                        shippingMethods.getPruductsByVendors(quote.getItems(), sa, countryId);
                    } else {
                        shippingMethods.getPruductsByVendors(quote.getItems(), quote.shippingAddress(), countryId);
                    }
                });

                $(document).on('click', ".action-select-shipping-item, .action-save-address", function () {
                    var countryId = quote.shippingAddress().countryId;
                    shippingMethods.getPruductsByVendors(quote.getItems(), quote.shippingAddress(), countryId);
                    $('[name="country_id"] > option:selected').val(countryId);
                });


                this._super();

                return this;
            },

            validateShippingInformation: function() {
                var parentResult = this._super();

                if (!parentResult) {
                    return false;
                }

                // supplier shipping terms and conditions
                var error_shipping_terms = false;

                $('.supplier_shipping_terms').each(function() {
                    var name = $(this).attr('name');
                    if(!$('input[name='+name+']:checked').is(':checked')) {
                        error_shipping_terms = true;
                    }
                });

                if (error_shipping_terms) {
                    this.errorValidationMessage($.mage.__('Please specify shipping terms and conditions for each supplier.'));
                    return false;
                }

                // supplier shipping methods
                var error_supplier_method = false;

                if ($('#s_method_supplier_supplier').is(':checked') || $('#s_method_supplier').is(':checked')) {
                    $('.supplier_methods').each(function() {
                        var name = $(this).attr('name');
                        if(!$('input[name='+name+']:checked').is(':checked')) {
                            error_supplier_method = true;
                        }
                    });
                }

                if (error_supplier_method) {
                    this.errorValidationMessage($.mage.__('Please specify shipping method for each supplier.'));
                    return false;
                }

                return true;

            },

            setShippingPrice: function() {
                $.ajax({
                    url: window.checkoutConfig.baseUrl+'marketplace/checkout/setshippingprice',
                    type: 'POST',
                    data: {
                        price: this.price,
                        method_id: this.id,
                        supplier_id: this.supplier_id,
                        currency_price: this.currency_price
                    },
                    dataType: 'json',
                    success: function (data) {
                        var price = priceUtils.formatPrice(
                            data.price_total.toFixed(2), window.checkoutConfig.priceFormat
                        );

                        $('#s_method_supplier_supplier')
                            .parent()
                            .next('td')
                            .html('<span><span class="text">'+ price +'</span></span>');

                        $('#s_method_supplier')
                            .parent()
                            .next('td')
                            .html('<span><span class="text">'+ price +'</span></span>');
                    }
                });

                return true;
            },
        });
    };
});
