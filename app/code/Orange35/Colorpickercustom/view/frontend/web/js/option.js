'use strict';

/**
 * @typedef OptionModel
 * @property {number} id
 * @property {boolean} multiple
 * @property {number[]} value
 */
define([
  'ko',
  'jquery',
  'underscore',
  'uiClass',
  './slick',
  'Magento_Swatches/js/swatch-renderer'
], function (ko, $, _, Class, Slick) {

  /** Slider
   *
   * Override orientationChange and window resize behavior since the slider feature is that
   * the number of swatches per slides automatically is defined based on container and swatch size
   */
  var Slider = function () {
    Slick.apply(this, arguments);
    this.setSlidesToShow(this.options.slidesToShow);
    if (this.options.initialSlide) {
      this.options.initialSlide = Math.floor(this.options.initialSlide/this.options.slidesToShow)
        * this.options.slidesToShow;
    }
  };
  Slider.prototype = Object.create(Slick.prototype);

  Slider.prototype.refreshConfig = function () {
    var count = Math.floor(this.$list.width() / this.options.swatchWidth);
    this.setSlidesToShow(count);
    this.setPosition();
  };

  Slider.prototype.setSlidesToShow = function (count) {
    if (this.options.maxSlidesToShow && this.options.maxSlidesToShow < count) {
      count = this.options.maxSlidesToShow;
    }
    this.options.slidesToShow = count;
    this.options.slidesToScroll = count;
  };

  Slider.prototype.orientationChange = function () {
    window.requestAnimationFrame(this.refreshConfig.bind(this));
  };

  Slider.prototype.resize = function () {
    if (this.rsTimer) {
      clearTimeout(this.rsTimer);
    }
    this.rsTimer = setTimeout(window.requestAnimationFrame.bind(window, this.refreshConfig.bind(this)), 500);
  };

  Slider.prototype.setDimensions = function () {
    Slick.prototype.setDimensions.apply(this, arguments);
    if (window.devicePixelRatio > 1) {
      // fix issue on 1.29 ratio when $slideTrack have not enough width to contain all sliders
      // and the last one wraps to new line
      this.$slideTrack.width(1 + this.$slideTrack.width());
    }
  };

  Slider.prototype.buildOut = function () {
    Slick.prototype.buildOut.apply(this, arguments);
    if (this.options.maxSlidesToShow) {
      this.$list.css('max-width', Math.ceil(this.options.maxSlidesToShow * this.options.swatchWidth));
    }
    this.$slider.trigger('afterBuildOut', this);
  };

  /** End Slider */

  return Class.extend({
    defaults: {
      ARROWS_WIDTH: 34,
      COMPONENT: 'Orange35_Colorpickercustom/js/option',
      CHOICE_METHOD_TOGGLE: null,
      CHOICE_METHOD_SELECT: null,

      choiceMethod: undefined,
      tooltip: false,

      id: undefined,
      multiple: false,
      /** custom option values */
      values: [],
      /** selected value ids */
      value: [],
      selectors: {},
      /** hidden input/select */
      element: undefined,
      swatches: undefined,
      selected: undefined,
      slider: {
        enabled: false,
        arrowsColor: '#000000',
        prevArrowSvg: '<svg class="btn-prev" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 44" fill="{color}"><path d="m 0,22 14,-22 2,0 -8,22 8,22 -2,0 Z"/></svg>',
        nextArrowSvg: '<svg class="btn-next" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 44" fill="{color}"><path d="m 16,22 -14,-22 -2,0 8,22 -8,22 2,0 Z"/></svg>',
        swatchesPerSlide: 0,
        options: {
          infinite: false,
          rows: 0,
          prevArrow: '<div class="btn-prev"></div>',
          nextArrow: '<div class="btn-next"></div>'
        }
      }
    },

    initialize: function () {
      this._super();
      this.value = ko.observableArray();
      this.initSelectors();
      this.initChoiceMethod();
      this.initSlider();
      this.element.on('change', _.debounce(this.elementOnChange.bind(this), 10)).change();
      this.tooltip && this.swatches.SwatchRendererTooltip();
      this.swatches.on('click', _.debounce(this.swatchOnClick.bind(this), 10));
      if (this.selected && this.selected.length) {
        this.value.subscribe(this.updateSelected.bind(this));
      }
      this.value.subscribe(this.highlight.bind(this));
      this.value(this.parseValue(this.element.val()));
    },

    initSelectors: function () {
      _.each(this.selectors, (function (selector, alias) {
        selector = selector.replace('{id}', this.id);
        this[alias] = $(selector);
        if (!this[alias].length) {
          throw new Error(this.COMPONENT + ': Can\'t find ' + alias + ' by selector ' + selector);
        }
      }).bind(this));
    },

    initChoiceMethod: function () {
      if (-1 === [this.CHOICE_METHOD_SELECT, this.CHOICE_METHOD_TOGGLE].indexOf(this.choiceMethod)) {
        throw new Error(this.COMPONENT + ': Unsupported choiceMethod - ' + this.choiceMethod);
      }
    },

    initSlider: function () {
      var swatch = this.swatches.first();
      var swatchesContainer = swatch.closest('.swatch-attribute-options');

      // clear css set in html to avoid content jumping
      swatchesContainer.css({overflowY: '', height: ''});

      if (!this.slider.enabled) {
        return;
      }

      this.swatchWidth = swatch.outerWidth(true);
      if (!this.slider.swatchesPerSlide && this.swatchWidth * this.swatches.length <= swatchesContainer.width()) {
        // no enough swatches for slider
        return;
      }
      var swatchesPerSlide = Math.floor((swatchesContainer.width() - this.ARROWS_WIDTH) / this.swatchWidth);
      if (!swatchesPerSlide) {
        return;
      }
      if (this.slider.swatchesPerSlide && this.slider.swatchesPerSlide < swatchesPerSlide) {
        swatchesPerSlide = this.slider.swatchesPerSlide;
      }
      if (this.swatches.length <= swatchesPerSlide) {
        // no enough swatches for slider
        return;
      }
      // wrap swatches into div to avoid stretching
      this.swatches.wrap('<div></div>');

      // move .o35-swatches class to a container which will be a parent for swatches after slider initialization
      swatchesContainer.removeClass('o35-swatches').on('afterBuildOut', (function (event, slider) {
        slider.$list.addClass('o35-swatches');
        slider.$prevArrow.css('background-image', this.getPrevArrowBackgroundImage());
        slider.$nextArrow.css('background-image', this.getNextArrowBackgroundImage());
      }).bind(this));

      var options = $.extend(true, {}, this.slider.options, {
        swatchWidth: this.swatchWidth,
        initialSlide: this.getInitialSlide(),
        slidesToShow: swatchesPerSlide,
        maxSlidesToShow: this.slider.swatchesPerSlide
      });
      new Slider(swatchesContainer.get(0), options);
    },

    getInitialSlide: function () {
      var value = this.value();
      if (!value.length) {
        return 0;
      }
      for (var i = 0; i < this.values.length; i++) {
        if (-1 !== value.indexOf(this.values[i].id)) {
          return i;
        }
      }
      return 0;
    },

    getPrevArrowBackgroundImage: function () {
      return 'url("data:image/svg+xml;charset=utf-8,' + encodeURIComponent(this.getPrevArrowSvg()) + '")';
    },

    getNextArrowBackgroundImage: function () {
      return 'url("data:image/svg+xml;charset=utf-8,' + encodeURIComponent(this.getNextArrowSvg()) + '")';
    },

    getPrevArrowSvg: function () {
      return this.slider.prevArrowSvg.replace('{color}', this.slider.arrowsColor);
    },

    getNextArrowSvg: function () {
      return this.slider.nextArrowSvg.replace('{color}', this.slider.arrowsColor);
    },

    elementOnChange: function () {
      this.setValue(this.element.val());
    },

    click: function (optionId, ctrlKey) {
      optionId = parseInt(optionId, 10);
      var value = this.value().slice(0), selected = -1 !== _.indexOf(value, optionId);
      var reset = false, select = false, unselect = false;

      switch (this.choiceMethod) {
        case this.CHOICE_METHOD_SELECT:
          reset = !this.multiple || this.multiple && !ctrlKey;
          unselect = this.multiple && ctrlKey && selected;
          select = !unselect;
          break;
        case this.CHOICE_METHOD_TOGGLE:
          reset = !this.multiple;
          select = !selected;
          unselect = this.multiple && selected;
          break;
      }
      if (reset) {
        value = [];
      }
      if (select) {
        value.push(optionId);
      }
      if (unselect) {
        value = _.without(value, optionId);
      }
      return value;
    },

    isEqual: function (array1, array2) {
      return 0 === _.difference(array1, array2).length && 0 === _.difference(array2, array1).length;
    },

    swatchOnClick: function (event) {
      var value = this.click(event.currentTarget.getAttribute('data-id'), event.ctrlKey);
      this.element.val(value).trigger('change');
    },

    updateSelected: function () {
      var titles = [];
      _.each(this.value(), (function (valueId) {
        var value = _.findWhere(this.values, {id: valueId});
        if (value) {
          titles.push(value.title);
        }
      }).bind(this));
      this.selected.text(titles.join(', '));
    },

    highlight: function () {
      var values = this.value();
      _.each(this.swatches, (function (swatch) {
        swatch = $(swatch);
        var id = parseInt(swatch.data('id'), 10);
        swatch.toggleClass('selected', -1 !== values.indexOf(id));
      }).bind(this));
    },

    parseValue: function (value) {
      value = _.isEmpty(value) ? [] : value;
      value = _.isArray(value) ? value : [value];
      value = _.map(value, function (value) {
        return parseInt(value, 10);
      });
      return value;
    },

    setValue: function (value) {
      if (!this.isEqual(value, this.value())) {
        this.value(this.parseValue(value));
      }
    },

    getValue: function () {
      if (!this.multiple) {
        return _.first(this.value());
      }
      return this.value();
    }
  });
});
