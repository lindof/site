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
        setBannerGetChecked: function (value){
            if(value == 0){
                this.bannerGetChecked = false;
            }else{
                this.bannerGetChecked = true;
            }
            this.visible(this.bannerGetChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.bannerGetChecked && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
