define([
    'Magento_Ui/js/form/element/file-uploader',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().bannerBadgeTriggerSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().bannerBadgeTriggerSupportingActions.indexOf(action) > -1);
        },
        setBadgeTriggerChecked: function (value){
            if(value == 0){
                this.badgeTriggerChecked = false;
            }else{
                this.badgeTriggerChecked = true;
            }
            this.visible(this.badgeTriggerChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.badgeTriggerChecked && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
