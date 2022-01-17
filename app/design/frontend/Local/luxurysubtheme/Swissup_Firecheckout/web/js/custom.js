define([
    'Swissup_Firecheckout/js/utils/form-field/validator',
    'mage/translate'
], function(validator, $t) {
    'use strict';

    // custom UK postcode validator
    // (Any custom validator should start from `fc-custom-rule` prefix)
    validator('[name="postcode"]', {
        'fc-custom-rule-no-spacing': {
            handler: function (value) {
                return !(new RegExp(/\s/).test(value));
            },
            message: $t('No space in Postal Code')
        }
    });
});
