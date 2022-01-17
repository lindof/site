define(
        [
            'jquery',
            'Magento_Checkout/js/view/summary/abstract-total',
            'Magento_Checkout/js/model/quote'
        ],
        function ($, Component, quote) {
            "use strict";
            return Component.extend({
                defaults: {
                    template: 'Biztech_Deliverydate/checkout/summary/deliverycharges'
                },
                isDeliveryChargesDisplayed: function () {
                    
                    var price = $('#delivery_charges').val();
                    if (price == null || price == '') {
                        $.each(quote.getTotals()._latestValue.total_segments, function (index, val) {
                            if (val.code == 'delivery_charges') {
                                price = val.value;
                            }
                        });
                    }
                    return window.deliverydateConfig.general.enabled && price != 0 && price != null;
                },
                getDeliveryCharges: function () {
                    var price = $('#delivery_charges').val();
                    if (price == null || price == '') {
                        $.each(quote.getTotals()._latestValue.total_segments, function (index, val) {
                            if (val.code == 'delivery_charges') {
                                price = val.value;
                            }
                        });
                    }
                    return this.getFormattedPrice(price);
                }
            });
        }
);