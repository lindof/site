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
        setBannerTriggerChecked: function (value){
            if(value == 0){
                this.bannerTriggerChecked = false;
            }else{
                this.bannerTriggerChecked = true;
            }
            this.visible(this.bannerTriggerChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.bannerTriggerChecked && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
