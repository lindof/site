define(
        [
            'ko',
            'jquery',
            'Magento_Checkout/js/model/quote',
            'Magento_Checkout/js/model/resource-url-manager',
            'mage/storage',
            'Magento_Checkout/js/model/payment-service',
            'Magento_Checkout/js/model/payment/method-converter',
            'Magento_Checkout/js/model/error-processor',
            'Magento_Checkout/js/model/full-screen-loader',
            'Magento_Checkout/js/action/select-billing-address'
        ],
        function (
            ko,
            $,
            quote,
            resourceUrlManager,
            storage,
            paymentService,
            methodConverter,
            errorProcessor,
            fullScreenLoader,
            selectBillingAddressAction
        ) {
            'use strict';

            return {
                saveShippingInformation: function () {

                    var payload;
                    if (!quote.billingAddress()) {
                        selectBillingAddressAction(quote.shippingAddress());
                    }

                    payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code,
                            extension_attributes: {
                                shipping_arrival_date: $('#shipping_arrival_date').val(),
                                shipping_arrival_slot: $('#shipping_arrival_slot').val(),
                                delivery_charges: $('#delivery_charges').val(),
                                shipping_arrival_comments: $('#shipping_arrival_comments').val(),
                                call_before_delivery: $('#delivery_date_callme').is(':checked'),
                            }
                        }
                    };

                    fullScreenLoader.startLoader();

                    return storage.post(
                            resourceUrlManager.getUrlForSetShippingInformation(quote),
                            JSON.stringify(payload)
                    ).done(
                        function (response) {
                            quote.setTotals(response.totals);
                            paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                            fullScreenLoader.stopLoader();
                        }
                    ).fail(
                        function (response) {
                            errorProcessor.process(response);
                            fullScreenLoader.stopLoader();
                        }
                    );
                }
            };
        }
);
