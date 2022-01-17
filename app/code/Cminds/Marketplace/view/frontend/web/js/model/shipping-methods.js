/**
 * Cminds Marketplace checkout cart shipping methods js component.
 *
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Cminds Team <info@cminds.com>
 */
define(
    [
        'ko',
        'jquery',
        'Magento_Catalog/js/price-utils'
    ],
    function (
        ko,
        $,
        priceUtils
    ) {
        'use strict';
        var data = [];
        var viewModel = {
            vendorsProducts: ko.observableArray(data)
        };
        return {
            getPruductsByVendors: function(items, shippingAddress, cid) {
                $.ajax({
                    url: window.checkoutConfig.baseUrl+'marketplace/checkout/getproductsbyvendors',
                    type: "POST",
                    showLoader: true,
                    data: {
                        json: JSON.stringify(items),
                        shippingAddress: JSON.stringify(shippingAddress),
                        cid: cid
                    },
                    dataType: 'json',
                    success: function (data) {
                        viewModel.vendorsProducts(data);

                        var priceTotal = 0;
                        var prices = [];
                        if (data.length >= 1) {
                            for (var i = 0; i < data.length; ++i) {
                                var methods = data[i].methods;
                                for (var j = 0; j < methods.length; ++j) {
                                    if (methods[j].checked) {
                                        priceTotal += parseFloat(methods[j].price);
                                    }
                                }
                            }
                        }

                        var price = priceUtils.formatPrice(
                            priceTotal.toFixed(2), window.checkoutConfig.priceFormat
                        );

                        $('#s_method_supplier_supplier')
                            .parent()
                            .next('td')
                            .html('<span><span class="text">'+ price +'</span></span>');
                    }
                });
            },

            getViewModel: function() {
                return viewModel.vendorsProducts;
            }
        }

    }
);
