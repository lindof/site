define([
    'Magento_Ui/js/form/element/single-checkbox',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().hintsNotSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().hintsNotSupportingActions.indexOf(action) == -1);
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
