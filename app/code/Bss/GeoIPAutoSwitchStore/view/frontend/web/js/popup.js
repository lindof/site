/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'Bss_GeoIPAutoSwitchStore/js/jquery.magnific-popup.min'
], function ($) {
    $.widget('bss.bss_config', {
        _create: function () {
            var options = this.options;
            var ajaxUrl = options.ajaxUrl;
            var currentUrl = options.currentUrl;
            var currentPath = options.currentPath;
            $(document).ready(function() {
                $.ajax({
                    showLoader: false,
                    url: ajaxUrl,
                    data : {
                        current_url: currentUrl,
                        current_path: currentPath
                    },
                    type: "POST",
                    dataType: 'json',
                    complete: function(response) {
                        var result = response.responseText;
                        result = JSON.parse(result);
                        if (result.status) {
                            var country_name = result.data.country_name;
                            var store_name = result.data.store_name;
                            var data_post = result.data.data_post;
                            var message = result.data.message;
                            var button = result.data.button;
                            message = message.replace(/\[country]/g, country_name);
                            message = message.replace(/\[store_name]/g, store_name);

                            var element_popup = '<div class="geoip_title">'+message+'</div><a href="'+data_post+'" ><button class="geoip_button">'+button+'</button></a>';
                            $(".popup_geoip_content").show();
                            $(".popup_geoip_content").html(element_popup);
                            openPopup();

                        }

                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });

            });

            function openPopup() {
                $.magnificPopup.open({
                    items: {
                        src: $(".popup_geoip_content"),
                        type: 'inline'
                    },
                    type: 'image',
                    closeOnBgClick: false,
                    scrolling: false,
                    preloader: true,
                    tLoading: '',
                    callbacks: {
                        close: function() {
                            $('.mfp-preloader').css('display', 'none');
                            $(".popup_geoip_content").hide();
                        }
                    }
                });
            }
        }
    });
    return $.bss.bss_config;
});
