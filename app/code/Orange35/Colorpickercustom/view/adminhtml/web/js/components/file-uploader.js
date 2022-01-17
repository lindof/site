define([
  'Magento_Ui/js/form/element/file-uploader'
], function (Element) {
  'use strict';

  return Element.extend({

    defaults: {
      imports: {
        visible: '${ $.parentName.replace(/\\.values\\.[\\d]+/, \'\') }.container_common.is_colorpicker:checked',
      }
    },

    initialize: function () {
      this._super();
      this.visible(this.visible());
    },

    /**
     * Defines initial value of the instance.
     *
     * @returns {FileUploader} Chainable.
     */
    setInitialValue: function () {
      var value = this.getInitialValue(), array = [];

      if (typeof value === 'string') {
        array[0] = {
          name: value,
          url: this.uploaderConfig.imageUrl + value
        };
        value = array;
      }

      value = value.map(this.processFile, this);

      this.initialValue = value.slice();

      this.value(value);
      this.on('value', this.onUpdate.bind(this));

      return this;
    }
  });
});
