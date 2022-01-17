define([
], function () {
    'use strict';
    return {
        "Mexbs_ApBase/js/model/ap-simple-actions": function(config){
            this.setConfig(config);
        },
        config: {},

        /**
         * Set configuration
         * @param {Object} config
         */
        setConfig: function (config) {
            this.config = config;
        },
        getConfig: function(){
            return this.config;
        }
    }
});