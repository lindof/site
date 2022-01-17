/**
 * Copyright © 2017, Magesales Consulting
 * See LICENSE for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'underscore',
        "mage/template"
    ],
    function () {
        'use strict';

        var config = window.checkoutConfig.payment.converge;

        return {
            isActive: function () {
                return config.isActive;
            },
            getFormType: function () {
                return config.formType;
            },
            isSandbox: function () {
                return config.isSandbox;
            },
            getRedirectUrl: function () {
                return config.redirectUrl;
            }
        };
    }
);
