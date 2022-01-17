'use strict';

define([
  'jquery',
  'underscore',
  'uiClass',
  './option'
], function ($, _, Class, Option) {

  return Class.extend({
    COMPONENT: 'Orange35_Colorpickercustom/js/options',
    option: {
      CHOICE_METHOD_TOGGLE: 'toggle',
      CHOICE_METHOD_SELECT: 'select',
      choiceMethod: undefined,
      selectors: {
        swatches: undefined
      }
    },
    selectors: {
      form: undefined,
      addToWishlistButton: undefined
    },
    options: [],
    form: undefined,
    addToWishlistButton: undefined,

    initialize: function () {
      this._super();
      this.initSelectors();
      if (this.addToWishlistButton && this.addToWishlistButton.length) {
        this.form.on('change', this.initAddToWishlistLink.bind(this));
      }
      this.createOptionElementsIfNotExist();
      this.initOptions();
    },

    initSelectors: function () {
      _.each(this.selectors, (function (selector, alias) {
        this.initSelector(alias);
      }).bind(this));
    },

    isNumber: function (value) {
      return /^[\d]+$/.test(value);
    },

    initSelector: function (alias) {
      var selector = this.selectors[alias], node, method, args;
      if (_.isObject(selector)) {
        node = $([]);
        for (var key in selector) {
          if (!selector.hasOwnProperty(key)) {
            continue;
          }
          if (this.isNumber(key)) {
            node = $(selector[key]);
            continue;
          }
          args = selector[key];
          args = _.isArray(args) ? args : [args];
          node = node[key].apply(node, args);
          if (!node.length) {
            throw new Error(this.COMPONENT + ': Can\'t find ' + alias + ' by chain selector');
          }
        }
        this[alias] = node;
      } else {
        this[alias] = $(selector, this.container);
      }
      if (!this[alias].length) {
        throw new Error(this.COMPONENT + ': Can\'t find ' + alias + ' by selector ' + selector);
      }
    },

    initOptions: function () {
      this.options = this.options.map(this.createOption.bind(this));
    },

    createOption: function (option, index) {
      return new Option($.extend(
        true,
        {},
        this.option,
        option,
        {
          selectors: {
            element: '#' + this.getOptionElementId(option.id)
          }
        }
      ));
    },

    getOptionElementId: function (optionId) {
      return this.form.find('[name^="options[' + optionId + ']"]').prop('id');
    },

    createOptionElementsIfNotExist: function ()
    {
      _.each(this.options, (function (option) {
        var name = 'options[' + option.id + ']';
        var element = this.form.find('[name^="' + name + '"]');
        if (!element.length) {
          element = this.createOptionElement(option);
          this.form.append(element);
        }
      }).bind(this));
    },

    /**
     * @param {OptionModel} option
     * @returns {HTMLElement}
     */
    createOptionElement: function (option) {
      var element;
      if (option.multiple) {
        element = document.createElement('SELECT');
        element.setAttribute('name', 'options[' + option.id + '][]');
        element.setAttribute('multiple', 'multiple');
        element.setAttribute('style', 'display:none');
        element.appendChild(document.createElement('option'));
        _.each(option.values, function (value) {
          var selectOption = document.createElement('option');
          selectOption.text = value.title;
          selectOption.value = value.id;
          element.appendChild(selectOption);
        });
      } else {
        element = document.createElement('INPUT');
        element.setAttribute('name', 'options[' + option.id + ']');
        element.setAttribute('type', 'hidden');
      }
      element.setAttribute('id', 'p-' + this.productId + '-o-' + option.id);

      return element;
    },

    initAddToWishlistLink: function () {
      var data = this.form.serializeArray(), element, key, valueId;
      var json = this.addToWishlistButton.data('post');
      json.data = {};
      for (var i = 0; i < data.length; i++) {
        element = data[i];
        if (!element.value.length) {
          continue;
        }
        if (element.name.substring(element.name.length - 2) === '[]') {
          key = element.name.substring(0, element.name.length - 2) + '[' + element.value + ']';
        } else {
          key = element.name;
        }
        json.data[key] = element.value;
      }
      json.qty = 1;
      this.addToWishlistButton.attr('data-post', JSON.stringify(json));
    }
  });
});
