var config = {
    map: {
        '*': {
            StoreLocator: 'IWD_StoreLocator/js/storelocator'
        }
    },
    path:{
        async: 'lib/require/async',
        goog: 'lib/require/goog'
    },
    shim: {
        'IWD_StoreLocator/js/jquery.visible.min': 'jquery',
        'IWD_StoreLocator/js/infobox':'google'
    }
};