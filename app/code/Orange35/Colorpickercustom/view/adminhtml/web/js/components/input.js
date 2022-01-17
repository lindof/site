define([
  'Magento_Ui/js/form/element/abstract',
], function (Abstract) {

  return Abstract.extend({
    defaults: {
      imports: {
        visible: '${ $.parentName }.is_colorpicker:checked',
      }
    },

    initialize: function () {
      this._super();
      this.visible(this.visible());
    }
  });
});