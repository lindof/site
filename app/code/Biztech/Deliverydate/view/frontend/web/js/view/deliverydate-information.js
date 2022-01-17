define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
    'moment'
], function ($, Component, quote, stepNavigator, sidebarModel , moment) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Biztech_Deliverydate/deliverydate-information'
        },

        /**
         * @return {Boolean}
         */
        isVisible: function () {
            return !quote.isVirtual() && stepNavigator.isProcessed('shipping') && window.deliverydateConfig.general.enabled && (this.getDeliveryDate() != "") ;
        },

        /**
         * Provide the delivery date
         * @return {String}
         */
        getDeliveryDate: function () {   
            var selecteddeliverydate = window.localStorage.getItem('selecteddeliverydate');
            var selectedslot = window.localStorage.getItem('selectedslot');
            if(selecteddeliverydate != '' && selecteddeliverydate != undefined && selectedslot != '' && selectedslot != undefined ){
                return moment(selecteddeliverydate,"DD-MM-YYYY").format("dddd, MMMM Do YYYY") + ' - ' + selectedslot;
            }else if(selecteddeliverydate != '' && selecteddeliverydate != undefined){
                return moment(selecteddeliverydate,"DD-MM-YYYY").format("dddd, MMMM Do YYYY");
            }else{ 
                return '';
            }
        },

        /**
         * Back to DeliveryDate selection.
         */
        backToDeliverydate: function () {
            sidebarModel.hide();
            stepNavigator.navigateTo('shipping', 'opc-shipping_method');
        }
    });
});
