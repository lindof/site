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
    'underscore'
], function ($) {
    'use strict';

    $.widget('bss.dcoControl', {
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
            $widget.collectState($widget);
        },
        updateDependentId: function ($widget) {
            var optionType = $widget.options.optionType,
                dcoData = $widget.options.dcoData.child;
            $widget.element.addClass('dco-uncommon')
            .attr('depend-id', $widget.options.dcoData.dependent_id);
            if (!window.dcoStatus.hasOwnProperty($widget.options.dcoData.dependent_id)) {
                $widget.element.addClass('dco-enabled');
            }
            if (optionType === 'drop_down' || optionType === 'multiple') {
                if ($widget.options.dcoRequire > 0) {
                    $widget.element.addClass('has-required').addClass('required');
                    $widget.element.find('select').addClass('required');
                }
                $widget.element.find($widget.options.optionClass).find('option').each(function () {
                    var value = $(this).attr('value'),
                        selected = $(this).prop('selected');
                    if (dcoData.hasOwnProperty(value)) {
                        dcoData[value].currentValue = false;
                        if (dcoData[value].depend_value) {
                            $widget.hasDependValue = true;
                        }
                        $(this).attr('depend-id', dcoData[value].dependent_id);
                        try {
                            $.each(dcoData[value].depend_value.split(','), function (dk, dvl) {
                                if (window.dcoStatus[dvl] === undefined) {
                                    window.dcoStatus[dvl] = {};
                                }
                                window.dcoStatus[dvl][value] = selected;
                            });
                        } catch (e) {
                            console.log('error when updateDependentId: ' + value);
                        }
                    }
                });
            } else if (optionType === 'radio' || optionType === 'checkbox') {
                if ($widget.options.dcoRequire > 0) {
                    $widget.element.addClass('has-required').addClass('required');
                    $widget.element.find('input').addClass('required');
                }
                $widget.element.find($widget.options.optionClass).each(function () {
                    var value = $(this).val(),
                        checked = $(this).prop('checked');
                    if (dcoData.hasOwnProperty(value)) {
                        dcoData[value].currentValue = $(this).prop('checked');
                        if (dcoData[value].depend_value) {
                            $widget.hasDependValue = true;
                        }
                        $(this).parents('div.choice').attr('depend-id', dcoData[value].dependent_id);
                        try {
                            $.each(dcoData[value].depend_value.split(','), function (dk, dvl) {
                                if (window.dcoStatus[dvl] === undefined) {
                                    window.dcoStatus[dvl] = {};
                                }
                                window.dcoStatus[dvl][value] = checked;
                            });
                        } catch (e) {
                            console.log('error when updateDependentId: ' + value);
                        }
                    } else {
                        if ($widget.options.dcoRequire > 0) {
                            $(this).parent().remove();
                        }
                    }
                });
            }
            $.each(window.dcoStatus, function (dk, dvl) {
                var value = $('[depend-id="'+dk+'"]');
                if (!value.hasClass('field') || value.hasClass('admin__field-option')) {
                    value.addClass('dco_value');
                }
            });
        },
        updateEventListener: function ($widget) {
            var optionType = $widget.options.optionType;

            $widget.element.on('dco-reset', function () {
                $widget.resetValue($widget, $(this));
            });
            $widget.element.on('dco-enable', function (e, flag) {
                $widget.enableOption($widget, $(this), flag);
            });
            $widget.element.on('dco-statecommit', function (e, flag) {
                $widget.stateCommit($widget, $(this), flag);
            });
            $widget.element.parents('#product-options-wrapper').on('scan-depend', function () {
                $widget.collectState($widget);
            });
            if ($widget.hasDependValue) {
                if (optionType === 'drop_down' || optionType === 'multiple') {
                    $widget.element.find($widget.options.optionClass).change(function () {
                        $widget.onOptionChange($widget, $(this));
                        $widget.collectState($widget);
                    });
                } else if (optionType === 'radio' || optionType === 'checkbox') {
                    $widget.element.find($widget.options.optionClass).click(function () {
                        $widget.onOptionClick($widget, $(this));
                        $widget.collectState($widget);
                    });
                }
            }
        },
        onOptionChange: function ($widget, element) {
            var values = element.find('option');
            values.each(function () {
                var value = $(this).val();
                if ($(this).prop('selected')) {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = true;
                        }
                    });
                } else {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = false;
                        }
                    });
                }
            });
        },
        onOptionClick: function ($widget, element) {
            var values = element.parents('.dco-uncommon').find('input');
            values.each(function () {
                var value = $(this).val();
                if ($(this).prop('checked')) {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = true;
                        }
                    });
                } else {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = false;
                        }
                    });
                }
            });
        },
        collectState: function ($widget) {
            var multipleParentValue = $widget.options.multipleParentValue,
                flag;
            $.each(window.dcoStatus, function (sk, svl) {
                var parent = $('[depend-id="' + sk + '"]').parents('.dco-uncommon'),
                    parentId = parent.attr('depend-id');
                if (multipleParentValue === 'atleast_one') {
                    flag = parent.hasClass('dco-enabled') && parent.length > 0 && window.dcoStatus.hasOwnProperty(sk) && window.dcoStatus.hasOwnProperty(parentId);
                } else if (multipleParentValue === 'all') {
                    flag = parent.length == 0 || parent.hasClass('dco-enabled') || !window.dcoStatus.hasOwnProperty(sk) || !window.dcoStatus.hasOwnProperty(parentId);
                }
                $.each(svl, function (sk2, svl2) {
                    if (multipleParentValue === 'atleast_one') {
                        flag = flag || svl2;
                    } else if (multipleParentValue === 'all') {
                        flag = flag && svl2;
                    }
                });
                $widget.triggerDepend(sk, flag);
            });
            $widget.triggerOption();
        },
        triggerOption: function () {
            var childrenDisplay = this.options.childrenDisplay;
            var multipleParentValue = this.options.multipleParentValue;
            $('.dco-uncommon').each(function () {
                if ($(this).find('[depend-id]:not(.dco-hide)').length == 0) {
                    $(this).trigger('dco-statecommit', false);
                } else {
                    $(this).trigger('dco-statecommit', true);
                }
            });
        },
        triggerDepend: function ($dependId, flag) {
            var childrenDisplay = this.options.childrenDisplay,
                element = $('[depend-id="' + $dependId + '"]'),
                $widget = this;
            if (childrenDisplay === 'hide') {
                if (flag) {
                    if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                        element.trigger('dco-enable', flag);
                        return;
                    } else {
                        element.fadeIn();
                        element.removeClass('dco-hide');
                    }
                } else {
                    if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                        element.trigger('dco-enable', flag);
                        element.trigger('dco-reset');
                        return;
                    } else if (element.prop('tagName') === 'OPTION') {
                        if (element.prop("selected")) {
                            element.prop("selected", false);
                            element.parents('select').trigger('change');
                        }
                    } else {
                        if (element.find('input').attr('type') === 'radio') {
                            var option = element.find('input').attr('id').split('_')[1],
                                changes = {},
                                currentState = element.find('input').prop("checked");
                            changes['options[' + option + ']'] = {
                                finalPrice: {amount: 0},
                                basePrice: {amount: 0}
                            };
                            if (currentState) {
                                element.find('input').prop("checked", false);
                                $('[data-role="priceBox"]').trigger('updatePrice', changes);
                                $widget.onOptionClick($widget, element.find('input'));
                                $widget.collectState($widget);
                            }
                        } else {
                            if (element.find('input').prop("checked")) {
                                element.find('input').trigger('click');
                            }
                        }
                    }
                    element.fadeOut();
                    element.addClass('dco-hide');
                }
            } else if (childrenDisplay === 'display') {
                if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                    element.trigger('dco-enable', flag);
                    if (!flag) {
                        element.trigger('dco-reset');
                    }
                } else if (element.prop('tagName') === 'OPTION') {
                    element.prop('disabled', !flag);
                    if (!flag && element.prop("selected")) {
                        element.prop("selected", false);
                        element.parents('select').trigger('change');
                    }
                } else {
                    element.find('input').prop('disabled', !flag);
                    if (element.find('input').attr('type') === 'radio') {
                        var option = element.find('input').attr('id').split('_')[1],
                            changes = {},
                            currentState = element.find('input').prop("checked");
                        changes['options[' + option + ']'] = {
                            finalPrice: {amount: 0},
                            basePrice: {amount: 0}
                        };
                        if (!flag && currentState) {
                            element.find('input').prop("checked", false);
                            $('[data-role="priceBox"]').trigger('updatePrice', changes);
                            $widget.onOptionClick($widget, element.find('input'));
                            $widget.collectState($widget);
                        }
                    } else {
                        if (!flag && element.find('input').prop("checked")) {
                            element.find('input').prop("checked", false).trigger('change');
                            $widget.onOptionClick($widget, element.find('input'));
                            $widget.collectState($widget);
                        }
                    }
                }
            }
        },

        resetValue: function ($widget, $this) {
            var changes = {};
            changes['options[' + $widget.options.optionId + ']'] = {
                finalPrice: {amount: 0},
                basePrice: {amount: 0}
            };
            switch ($widget.options.optionType) {
                case 'radio':
                    if ($this.find('input:checked').length > 0) {
                        $this.find('input:checked').each(function () {
                            $(this).prop('checked', false);
                            $widget.onOptionClick($widget, $(this));
                            $widget.collectState($widget);
                        });
                        $('[data-role="priceBox"]').trigger('updatePrice', changes);
                    }
                    break;
                case 'checkbox':
                    if ($this.find('input:checked').length > 0) {
                        $this.find('input:checked').each(function () {
                            $(this).prop('checked', false).trigger('change');
                            $widget.onOptionClick($widget, $(this));
                            $widget.collectState($widget);
                        });
                    }
                    break;
                case 'drop_down':
                    if (($this.find('.product-custom-option').val() !== '') ) {
                        $this.find('option').prop('selected', false);
                        $this.find('select').trigger('change');
                    }
                    break;
                case 'multiple':
                    if ($this.find('option:selected').length > 0) {
                        $this.find('option').prop('selected', false);
                        $this.find('select').trigger('change');
                    }
                    break;
            }
        },
        enableOption: function ($widget, $this, flag) {
            var dcoRequire = $widget.options.dcoRequire;
            if (!flag) {
                $this.removeClass('dco-enabled');
                if ($widget.options.childrenDisplay === 'hide') {
                    $this.fadeOut();
                    $this.find('[depend-id]:not(.dco_value)').fadeOut();
                }
            } else {
                $this.addClass('dco-enabled');
                if ($widget.options.childrenDisplay === 'hide') {
                    $this.fadeIn();
                    $this.find('[depend-id]:not(.dco_value)').fadeIn();
                }
            }
            switch ($widget.options.optionType) {
                case 'radio':
                case 'checkbox':
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('input').hasClass('required')) {
                            $this.find('input').addClass('required');
                        } else if (!flag && $this.find('input').hasClass('required')) {
                            $this.find('input').removeClass('required');
                        }
                    }
                    break;
                case 'drop_down':
                case 'multiple':
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('select').hasClass('required')) {
                            $this.find('select').addClass('required');
                        } else if (!flag && $this.find('select').hasClass('required')) {
                            $this.find('select').removeClass('required');
                        }
                    }
                    break;
            }
        },
        stateCommit: function ($widget, $this, flag) {
            var dcoRequire = $widget.options.dcoRequire;
            if ($this.find('.dco_value').length !== $this.find('[depend-id]').length) {
                flag = $this.hasClass('dco-enabled');
            }
            if ($widget.options.childrenDisplay === 'hide') {
                if (flag || $this.find('.dco_value:not(.dco-hide)').length > 0) {
                    $this.fadeIn();
                } else {
                    $this.fadeOut();
                }
            }
            switch ($widget.options.optionType) {
                case 'radio':
                case 'checkbox':
                    $this.find('[depend-id]:not(.dco_value)').find('input').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('input').hasClass('required')) {
                            $this.find('input').addClass('required');
                        } else if (!flag && $this.find('input').hasClass('required')) {
                            $this.find('input').removeClass('required');
                        }
                    }
                    break;
                case 'drop_down':
                case 'multiple':
                    $this.find('[depend-id]:not(.dco_value)').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('select').hasClass('required')) {
                            $this.find('select').addClass('required');
                        } else if (!flag && $this.find('select').hasClass('required')) {
                            $this.find('select').removeClass('required');
                        }
                    }
                    break;
            }
        }
    });
    return $.bss.dcoControl;
});
