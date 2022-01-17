
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magesales_Converge/payment/form/merchant',
                code: 'converge',
                redirectAfterPlaceOrder: true
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                return this.getCode() === this.isChecked();
            },

            /**
             * @returns {Array}
             */
            getData: function () {
                var month = $(this.getSelector('exp')).val(),
                    year = $(this.getSelector('exp_yr')).val().slice(-2);
                month = month < 10 ? '0' + month : month;

                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_number': $(this.getSelector('cc')).val(),
                        'cc_exp_date': month + year,
                        'cc_exp_month': month,
                        'cc_exp_year': year,
                        'cc_cid': $(this.getSelector('cvv')).val(),
                        'cc_type': $(this.getSelector('cc_type')).val()
                    }
                };
            },

            /**
             * Validates form
             */
            validate: function () {
                return true;
            },

            /**
             * @param field
             * @returns {string}
             */
            getSelector: function (field) {
                return '#' + this.getCode() + '_' + field;
            }
        });
    }
);
