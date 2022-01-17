define([
  'Magento_Ui/js/form/element/select',
], function (Select) {

  return Select.extend({
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