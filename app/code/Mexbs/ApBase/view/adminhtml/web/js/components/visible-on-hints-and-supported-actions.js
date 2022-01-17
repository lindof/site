define([
    'Magento_Ui/js/form/element/abstract',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().hintsNotSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().hintsNotSupportingActions.indexOf(action) == -1);
        },
        setCartHintsChecked: function (value){
            if (value == 0){
                this.cartHintsChecked = false;
            }else{
                this.cartHintsChecked = true;
            }
            this.visible(this.cartHintsChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.cartHintsChecked && this.isInSupportedActionArray(this.simpleAction));
        }
    });
});
