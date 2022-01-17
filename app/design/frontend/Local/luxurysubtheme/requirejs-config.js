
var config = {
  map: {
      "*": {
          "lazysizes": "js/lazysizes.min",
          "productoptioncallapse": "js/productcollapse.min"
      }
  },
  shim: {
      'js/lazysizes.min': {
          'deps': ['jquery']
      },
      'js/productcollapse.min': {
        'deps': ['jquery']
      }
    }
};

