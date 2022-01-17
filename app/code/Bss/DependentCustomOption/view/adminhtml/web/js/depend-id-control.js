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
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function ($, _, abstract) {
    'use strict';

    return abstract.extend({
        defaults: {
            bssOptionsKey: '',
            bssValueKey: '',
            bssTempDependId: ''
        },
        initConfig: function () {
            this._super();
            var namePart = this.name.split('.');
            this.bssOptionsKey = namePart[4];
            if (window.bss_depend_id === undefined) {
                window.bss_depend_id = {};
            }
            if (window.bss_depend_option === undefined) {
                window.bss_depend_option = {};
            }
            if (window.bss_depend_option[this.bssOptionsKey] === undefined) {
                window.bss_depend_option[this.bssOptionsKey] = {};
            }
            return this;
        },
        getDependValue: function () {
            if (this.value() === '0' || this.value() === '') {
                if (this.bssTempDependId === '') {
                    var jsonUrl = this.jsonUrl,
                        $this = this;
                    $.ajax({
                        type: 'get',
                        url: jsonUrl,
                        dataType: 'json',
                        success : function ($data) {
                            $this.bssTempDependId = $data;
                            $('#bss-dco-span-' + $this.uid).html($data);
                            $this
                            window.bss_depend_id[$data] = true;
                            window.bss_depend_option[$this.bssOptionsKey][$data] = true;
                            $this.value($data);
                        }
                    });
                }
            } else {
                window.bss_depend_id[this.value()] = true;
                window.bss_depend_option[this.bssOptionsKey][this.value()] = true;
                return this.value();
            }
        },
        getSpanId: function () {
            window.dcoChanged = true;
            return 'bss-dco-span-' + this.uid;
        },
        getDependClass: function () {
            return 'dependent-id option-' + this.bssOptionsKey;
        }
    });
});
