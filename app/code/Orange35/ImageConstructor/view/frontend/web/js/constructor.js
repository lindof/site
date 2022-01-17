define([
  'jquery',
  'underscore'
], function ($, _) {
  "use strict";

  $.widget('orange35.imageConstructor', {
    options: {
      nodes: {
        galleryPlaceholder: '[data-gallery-role="gallery-placeholder"]',
        gallery: '[data-gallery-role="gallery"]',
        form: '#product_addtocart_form',
        imageContainer: '.fotorama__stage__frame',
        zoomImage: '.fotorama__img--full'
      },
      zoomMode: '.fotorama--fullscreen',
      layers: [],
      productId: undefined,
      mergeUrl: undefined,
      singleImageStrategy: false
    },

    _create: function () {
      this.initialStateChecksum = this.stateChecksum = 'l-';
      this.mergedImages = {};
      this.requestAnimationFrame = (window.requestAnimationFrame
        || window.mozRequestAnimationFrame
        || window.webkitRequestAnimationFrame
        || window.msRequestAnimationFrame).bind(window);

      this.update = _.debounce(this.renderLayers.bind(this), 10);
      this.get('galleryPlaceholder').one('gallery:loaded', _.bind(this.galleryOnLoad, this));
      window.orange35 = window.orange35 || {updatingGallery: false};
    },

    initMainImage: function () {
      var images = this.gallery.returnCurrentImages();
      this.mainImage = _.find(images, function (image) {
        return image.isMain;
      });
      if (!this.mainImage) {
        this.mainImage = images[0];
      }
    },

    galleryOnLoad: function (event) {
      this.gallery = this.get('galleryPlaceholder').data('gallery');

      this.get('form').on('change', this.update);
      if (!this.options.singleImageStrategy) {
        this.get('gallery').on('fotorama:load', this.update);
        // this.get('gallery').on('fotorama:ready', this.update);
      }
      this.defaultImages = this.gallery.returnCurrentImages();
    },

    restoreDefaultImages: function () {
      window.orange35.updatingGallery = true;
      this.gallery.updateData(this.defaultImages);
      window.orange35.updatingGallery = false;
    },

    get: function (alias, context) {
      return $(this.options.nodes[alias], context);
    },

    basename: function (path) {
      return path.substring(path.lastIndexOf('/') + 1);
    },

    getMainImageFrame: function () {
      if (!this.mainImage) {
        return $([]);
      }
      return this.get('imageContainer').filter('[href$="' + this.basename(this.mainImage.full) + '"]');
    },

    renderLayers: function () {
      var layers = this.getChosenLayers();
      var layerIds = _.map(layers, function (layer) {return layer.valueId});
      var zoomMode = this.get('gallery').is(this.options.zoomMode);
      var checksum = (zoomMode ? 'z' : '')+ 'l-' + layerIds.join('-');
      if (this.stateChecksum === checksum) {
        return;
      }
      this.stateChecksum = checksum;
      if (checksum === this.initialStateChecksum) {
        return this.restoreDefaultImages();
      }
      this.initMainImage();

      if (this.options.singleImageStrategy) {
        this.mergeLayers(layerIds).done(this.replaceImage.bind(this));
      } else {
        this.preloadLayerImages(layers, (function() {
          var $frame = this.getMainImageFrame();
          var $images = $frame.find('img').not('[data-o35-layer]');
          var render = (function () {
            $images.map((function(index, image) {
              this.renderImageLayers($(image), layers, $frame);
            }).bind(this));
            this.hideProgress();
          }).bind(this);
          this.requestAnimationFrame(render);
        }).bind(this));
      }
    },

    replaceImage: function (mergedImage)
    {
      var image, images = this.gallery.returnCurrentImages();
      for (var i = 0; i < images.length; i++) {
        image = images[i];
        if (image.id === mergedImage.id) {
          image.full = mergedImage.largeImage;
          image.img = mergedImage.mediumImage;
          image.thumb = mergedImage.smallImage;
          break;
        }
      }
      window.orange35.updatingGallery = true;
      this.gallery.updateData(images);
      window.orange35.updatingGallery = false;
    },

    mergeLayers: function (layerIds) {
      var key = this.mainImage.id + '/' + layerIds.join('-');
      var df = $.Deferred();
      if (this.mergedImages.hasOwnProperty(key)) {
        df.resolve(this.mergedImages[key]);
        return df;
      }
      this.showProgress();
      var url = this.options.mergeUrl
        + 'product/' + this.options.productId
        + '/image/' + this.mainImage.id
        + '/layers/' + layerIds.join('-');

      $.get({
        url: url,
        dataType: 'json'
      }).success((function (response) {
        this.mergedImages[key] = response.image;
        df.resolve(this.mergedImages[key]);
        this.hideProgress();
      }).bind(this));
      return df;
    },

    preloadLayerImages: function (layers, callback) {
      var i, count = layers.length, layer, image, allLoaded = true;

      for (i = 0; i < layers.length; i++) {
        if (!layers[i].imageLoaded) {
          allLoaded = false;
          break;
        }
      }

      if (!allLoaded) {
        this.showProgress();
      }

      for (i = 0; i < layers.length; i++) {
        layer = layers[i];
        if (layer.imageLoaded) {
          count--;
          continue;
        }
        image = new Image();
        image.onload = (function (layer) {
          layer.imageLoaded = true;
          if (--count === 0) {
            return callback.call();
          }
        }).bind(this, layer);
        image.src = layer.mediumImage;
      }
      if (count === 0) {
        return callback.call();
      }
    },

    showProgress: function () {
      $('.fotorama__spinner').show();
    },

    hideProgress: function () {
      $('.fotorama__spinner').hide();
    },

    renderImageLayers: function ($image, layers, $frame) {
      var layer, $layer, i,
        isLarge = $image.is(this.options.nodes.zoomImage),
        type = isLarge ? 'large' : 'medium',
        fragment = document.createDocumentFragment();

      $frame.find('img').filter('[data-o35-layer=' + type + ']').remove();

      for (i = 0; i < layers.length; i++) {
        layer = layers[i];
        $layer = $image.clone()
          .attr('data-o35-layer', type)
          .attr('data-id', layer.valueId)
          .attr('src', isLarge ? layer.largeImage : layer.mediumImage);
        fragment.appendChild($layer.get(0));
      }
      $frame.get(0).appendChild(fragment);
    },

    getChosenLayers: function () {
      var layers = [], layer;
      var data = this.get('form').serializeArray();
      for (var i = 0; i < data.length; i++) {
        if (0 === data[i].name.indexOf('options[') && data[i].value !== "") {
          layer = _.findWhere(this.options.layers, {valueId: data[i].value});
          if (layer && !_.isNull(layer.largeImage)) {
            layers.push(layer);
          }
        }
      }
      _.sortBy(layers, function (l) {
        return l.sortOrderOption;
      });
      return layers;
    }
  });

  return $.orange35.imageConstructor;
});
