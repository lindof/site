define([
    'Magento_Ui/js/form/element/file-uploader',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().bannerBadgeGetSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().bannerBadgeGetSupportingActions.indexOf(action) > -1);
        },
        setBadgeGetChecked: function (value){
            if(value == 0){
                this.badgeGetChecked = false;
            }else{
                this.badgeGetChecked = true;
            }
            this.visible(this.badgeGetChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.badgeGetChecked && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
