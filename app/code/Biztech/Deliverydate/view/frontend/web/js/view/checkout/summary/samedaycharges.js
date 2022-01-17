define([
    "Magento_Checkout/js/view/summary/abstract-total",
    "Magento_Checkout/js/model/quote",
    "Magento_Catalog/js/price-utils",
    "Magento_Checkout/js/model/totals",
    "jquery",
    "moment",
], function (Component, quote, priceUtils, totals, $, moment) {
    "use strict";
    return Component.extend({
        defaults: {
            isFullTaxSummaryDisplayed:
                window.checkoutConfig.isFullTaxSummaryDisplayed || false,
            template: "Biztech_Deliverydate/checkout/summary/samedaycharges",
        },
        isDeliveryChargesDisplayed: function () {
            var price = 0;
            if (this.totals() && totals.getSegment("same_day_charges")) {
                price = totals.getSegment("same_day_charges").value;
            }
            return (
                price != 0 &&
                window.deliverydateConfig.general.enabled &&
                price != null
            );
        },
        totals: quote.getTotals(),
        isTaxDisplayedInGrandTotal:
            window.checkoutConfig.includeTaxInGrandTotal || false,
        getValue: function () {
            var price = 0;
            if (this.totals() && totals.getSegment("same_day_charges")) {
                price = totals.getSegment("same_day_charges").value;
            }
            return this.getFormattedPrice(price);
        },
        getDate: function () {
            var selecteddeliverydate = window.localStorage.getItem(
                "selecteddeliverydate"
            );
            var selectedslot = window.localStorage.getItem("selectedslot");
            if (
                selecteddeliverydate != "" &&
                selecteddeliverydate != undefined &&
                selectedslot != "" &&
                selectedslot != undefined
            ) {
                return (
                    moment(selecteddeliverydate, "DD-MM-YYYY").format(
                        "dddd, MMMM Do YYYY"
                    ) +
                    " - " +
                    selectedslot
                );
            } else if (
                selecteddeliverydate != "" &&
                selecteddeliverydate != undefined
            ) {
                return moment(selecteddeliverydate, "DD-MM-YYYY").format(
                    "dddd, MMMM Do YYYY"
                );
            } else {
                return "";
            }
        },
    });
});
