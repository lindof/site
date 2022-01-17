define([
    'jquery'
], function (
    $
    ) {
    'use strict';

    return {
        injectMessages: function (afterObject, urlToFetchMessages) {
            $(".cart-extra-messages").remove();
            $.ajax({
                url: urlToFetchMessages,
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    if(!$.isEmptyObject(response)){
                        var extraMessagesDivHtml = '<div class="cart-extra-messages">';
                        var extraMessage = "";
                        for(var extraMessageIndex in response){
                            if(response.hasOwnProperty(extraMessageIndex)){
                                extraMessage = response[extraMessageIndex];
                            }
                            if(extraMessage != ""){
                                extraMessagesDivHtml = extraMessagesDivHtml + '<div class="extra-message">' + extraMessage + '</div>';
                            }
                        }
                        extraMessagesDivHtml += '</div>';
                        $(extraMessagesDivHtml).insertAfter(afterObject);
                    }
                }
            });
        }
    };
});
