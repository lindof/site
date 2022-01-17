define([
    'jquery',
    'uiClass',
    'underscore',
    'ColorPicker'
], function ($, Class, _) {
    'use strict';

    return Class.extend({

        initialize: function () {
            this.initColorPicker('iwd_storelocator_design_fill_color');
            this.initColorPicker('iwd_storelocator_design_stroke_color');
            return this;
        },

        initColorPicker: function (elementId) {
            var element = document.getElementById(elementId);

            $('#' + elementId).ColorPicker({
                onSubmit: function (hsb, hex, rgb, el) {
                    $(el).val(hex);
                    $(el).ColorPickerHide();
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                }
            }).bind('keyup', function () {
                $(this).ColorPickerSetColor(this.value);
            });
        },
    });
});
