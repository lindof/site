define([
    'Magento_Ui/js/form/element/abstract',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().promoBlockSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().promoBlockSupportingActions.indexOf(action) > -1);
        },
        setPromoBlockInCartChecked: function (value){
            if (value == 0){
                this.promoBlockInCartChecked = false;
            }else{
                this.promoBlockInCartChecked = true;
            }
            this.visible((this.promoBlockInCartChecked || this.promoBlockInProductChecked) && this.isInSupportedActionArray(this.simpleAction));
        },
        setPromoBlockInProductChecked: function (value){
            if (value == 0){
                this.promoBlockInProductChecked = false;
            }else{
                this.promoBlockInProductChecked = true;
            }
            this.visible((this.promoBlockInCartChecked || this.promoBlockInProductChecked) && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible((this.promoBlockInCartChecked || this.promoBlockInProductChecked) && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
