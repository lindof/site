define([
    'jquery',
    'underscore',
    'priceUtils'
], function ($, _, utils) {
    'use strict';
    return function (widget) {

        $.widget('mage.priceOptions', widget, {            
            _onOptionChanged: function onOptionChanged(event) {
                var changes,
                    option = $(event.target),
                    handler = this.options.optionHandlers[option.data('role')];

                option.data('optionContainer', option.closest(this.options.controlContainer));

                if (handler && handler instanceof Function) {
                    changes = handler(option, this.options.optionConfig, this);
                } else {
                    changes = this.defaultGetOptionValue(option, this.options.optionConfig);
                }
                $(this.options.priceHolderSelector).trigger('updatePrice', changes);
            },
            defaultGetOptionValue: function (element, optionsConfig) {
                var changes = {},
                    optionValue = element.val(),
                    optionId = utils.findOptionId(element[0]),
                    optionName = element.prop('name'),
                    optionType = element.prop('type'),
                    optionConfig = optionsConfig[optionId],
                    optionHash = optionName;

                switch (optionType) {
                    case 'text':
                    case 'textarea':
                        changes[optionHash] = optionValue ? optionConfig.prices : {};
                        break;

                    case 'radio':
                        if (element.is(':checked')) {
                            changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                        }
                        break;

                    case 'select-one':
                        changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                        break;

                    case 'select-multiple':
                        _.each(optionConfig, function (row, optionValueCode) {
                            optionHash = optionName + '##' + optionValueCode;
                            changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                        });
                        break;

                    case 'checkbox':
                        optionHash = optionName + '##' + optionValue;
                        changes[optionHash] = element.is(':checked') ? optionConfig[optionValue].prices : {};
                        break;

                    case 'file':
                        // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                        changes[optionHash] = optionValue || (element.prop('disabled') && !element.parents('.field.file').hasClass('dco-option')) ? optionConfig.prices : {};
                        break;
                }

                return changes;
            }
        });

        return $.mage.priceOptions;
    }
});
