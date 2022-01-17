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
    'jquery'
], function ($) {
    'use strict';

    $.widget('bss.dcoOption', {
        options: {
            optionWrapper: '#product-options-wrapper',
            optionClass: '.product-custom-option'
        },
        hasDependValue: false,
        _create: function () {
            var $widget = this;
            if (window.dcoStatus === undefined) {
                window.dcoStatus = {};
            }
            $widget.updateDependentId($widget);
            $widget.updateEventListener($widget);
            $widget.element.parents('#product-options-wrapper').trigger('scan-depend');
        },
        updateDependentId: function ($widget) {
            $widget.element.attr('depend-id', $widget.options.dependId).addClass('dco-option');
            if ($widget.options.dcoRequire > 0) {
                $widget.element.addClass('required');
                $widget.enableOption($widget, $widget.element, true);
            }
        },
        updateEventListener: function ($widget) {
            $widget.element.on('dco-reset', function () {
                $widget.resetValue($widget, $(this));
            });
            $widget.element.on('dco-enable', function (e, flag) {
                $widget.enableOption($widget, $(this), flag);
            });
        },
        resetValue: function ($widget, $this) {
            switch ($widget.options.optionType) {
                case 'field':
                    $this.find('input[type="text"]').val('').trigger('change');
                    break;
                case 'area':
                    $this.find('textarea').val('').trigger('change');
                    break;
                case 'file':
                    $this.find('input[type="file"]').val("").trigger('change');
                    break;
                case 'date':
                case 'date_time':
                case 'time':
                    $this.find('select option').prop('selected', false);
                    $this.find('select').trigger('change');
                    break;
            }
        },
        enableOption: function ($widget, $this, flag) {
            var dcoRequire = $widget.options.dcoRequire;
            if (this.options.childrenDisplay === 'hide') {
                if (flag) {
                    $this.addClass('dco-enabled');
                    $this.removeClass('dco-hide');
                    $this.fadeIn();
                } else {
                    $this.removeClass('dco-enabled');
                    $this.addClass('dco-hide');
                    $this.fadeOut();
                }
            }
            switch ($widget.options.optionType) {
                case 'field':
                    $this.find('input[type="text"]').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag) {
                            $this.find('input[type="text"]').addClass('required');
                        } else {
                            $this.find('input[type="text"]').removeClass('required');
                        }
                    }
                    break;
                case 'area':
                    $this.find('textarea').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag) {
                            $this.find('textarea').addClass('required');
                        } else {
                            $this.find('textarea').removeClass('required');
                        }
                    }
                    break;
                case 'file':
                    $this.find('input[type="file"]').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag) {
                            $this.find('input[type="file"]').addClass('required');
                        } else {
                            $this.find('input[type="file"]').removeClass('required');
                        }
                    }
                    break;
                case 'date':
                case 'date_time':
                case 'time':
                    $this.find('select').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag) {
                            $this.find('fieldset').addClass('required');
                            $this.find('select').addClass('required');
                        } else {
                            $this.find('fieldset').removeClass('required');
                            $this.find('select').removeClass('required');
                        }
                    }
                    break;
            }
        }
    });
    return $.bss.dcoOption;
});
