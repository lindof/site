/**
 * ArgentoSticky allows to create sicky elements with media rules
 */

define([
    'jquery',
    'matchMedia',
    'Magento_Ui/js/modal/modal', // 2.3.3: create 'jquery-ui-modules/widget' dependency
    'js/lib/sticky-kit'
], function ($, mediaCheck) {
    'use strict';

    $.widget('argento.argentoSticky', {
        options: {
            // media: '(min-width: 768px) and (min-height: 600px)',
            // parent: $('.page-wrapper'),
            // delay: 150
        },

        /**
         * Widget initialization
         */
        _create: function () {
            if (this.options.media) {
                mediaCheck({
                    media: this.options.media,
                    entry: $.proxy(function () {
                        // this option appears after disable() call
                        if (this.options.disabled) {
                            return;
                        }

                        if (this.options.delay) {
                            if (this.timer) {
                                clearTimeout(this.timer);
                            }

                            this.timer = setTimeout(
                                this._stick.bind(this),
                                this.options.delay
                            );
                        } else {
                            this._stick();
                        }
                    }, this),
                    exit: this._unstick.bind(this)
                });
            } else {
                this._stick();
            }
        },

        /**
         * @return {this}
         */
        _stick: function () {
            if (this.element) {
                //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                //NE 20211022 - Fix for Uncaught TypeError: this.element.stick_in_parent is not a function error causing  javascript code block
                try {
                    this.element.stick_in_parent(this.options);
                } catch (e) {
                    console.log(e);
                }
                //jscs:enable requireCamelCaseOrUpperCaseIdentifiers
            }

            return this;
        },

        /**
         * @return {this}
         */
        _unstick: function () {
            if (this.timer) {
                clearTimeout(this.timer);
            }

            if (this.element) {
                this.element.trigger('sticky_kit:detach');
            }

            return this;
        },

        /**
         * Enable sticky element
         */
        enable: function () {
            return this._stick()._super();
        },

        /**
         * Disable sticky element
         */
        disable: function () {
            return this._unstick()._super();
        }
    });

    return $.argento.argentoSticky;
});
