/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Magesales_Converge/js/model/config'
    ],
    function (Component, rendererList, config) {
        'use strict';

        if (config.isActive()) {
            rendererList.push(
                {
                    type: 'converge',
                    component: 'Magesales_Converge/js/view/payment/method-renderer/' + config.getFormType()
                }
            );
        }

        return Component.extend({});
    }
);
