var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default": "Biztech_Deliverydate/js/shipping-save-processor-default-override",
            'calendar': 'mage/calendar'
        }
    },
    'paths': {
        'mousewheel': 'Biztech_Deliverydate/js/jquery.mousewheel.min'
    },
    'shim': {
        'mousewheel': {
            exports: 'mousewheel',
            'deps': ['jquery']
        }
    }
};