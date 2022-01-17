/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'ko',
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'
], function (ko, $, _, abstract, validator) {
    'use strict';

    return abstract.extend({
        defaults: {
            bssOptionsKey: '',
            multiselectId: '',
            multiselectList: ko.observableArray(),
            multiselectValue: ko.observableArray(),
        },
        initConfig: function () {
            this._super();
            if (window.dcoChanged === undefined) {
                window.dcoChanged = false;
            }
            var namePart = this.name.split('.');
            this.bssOptionsKey = namePart[4];
            this.multiselectId = 'multiselect-' + this.uid;
            validator.addRule('dependent-id-exist', this.validateDependentExist, $.mage.__("Please add the valid IDs"));
            validator.addRule('dependent-id-option', this.validateDependentOption, $.mage.__("Parent and children values can't be in the same custom option"));
            this.validation = {'dependent-id-exist': true, 'dependent-id-option': true};
            this.validationParams = {'dependent-option-key': this.bssOptionsKey, 'this': this};
            return this;
        },
        multiselectOption: function () {
            var $this = this,
                currentIds = window.bss_depend_option[this.bssOptionsKey];
            $this.multiselectList.removeAll();
            $.each(window.bss_depend_id, function (k, id) {
                if (currentIds[k] === undefined) {
                    $this.multiselectList.push(k);
                }
            });
            $this.multiselectValue.removeAll();
            $.each(this.value().split(','), function (k, vl) {
                $this.multiselectValue.push(vl);
            });
        },
        multiselectValue: function () {
            return this.value().split(',');
        },
        updateDependChild: function () {
            if ($('#' + this.multiselectId).val()) {
                this.value($('#' + this.multiselectId).val().join(','));
            }
        },
        expandMultiselect: function () {
            if ($('#' + this.multiselectId).hasClass('show')) {
                $('#' + this.multiselectId).fadeOut().removeClass('show');
                $('#' + this.uid).fadeIn();
            } else {
                this.multiselectOption();
                $('#' + this.uid).fadeOut();
                $('#' + this.multiselectId).fadeIn().addClass('show');
            }
        },
        validateDependentExist: function (value, params, additionalParams) {
            if (value === '' || !window.dcoChanged) {
                return true;
            }
            var ids = value.split(','),
                result = true;
            $.each(ids, function (k, val) {
                if (window.bss_depend_id[val] === undefined) {
                    result = false;
                }
            });
            return result;
        },
        validateDependentOption: function (value, params, additionalParams) {
            if (!window.dcoChanged) {
                return true;
            }
            var ids = value.split(','),
                result = true,
                currentIds = window.bss_depend_option[additionalParams['dependent-option-key']];
            $.each(currentIds, function (k, id) {
                if (ids.indexOf(k) >= 0) {
                    result = false;
                }
            });
            return result;
        }
    });
});
