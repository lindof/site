'use strict';

define([
  'Magento_Ui/js/lib/view/utils/async',
  'Magento_Ui/js/form/element/abstract',
  'orange35ColorPickerElement'
], function ($, Element, ColorPicker) {

  return Element.extend({
    defaults: {
      imports: {
        visible: '${ $.parentName.replace(/\\.values\\.[\\d]+/, \'\') }.container_common.is_colorpicker:checked'
      }
    },

    initialize: function () {
      this._super();
      this.visible(this.visible());
      $.async('#' + this.uid, function (element) {
        new ColorPicker({}, element);
      });
      return this;
    }
  });
});
