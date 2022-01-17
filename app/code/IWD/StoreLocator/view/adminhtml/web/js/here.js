define([
    "jquery"
], function ($) {
    'use strict';
    let StoreLocator = function () {
        this.init = function (appId, appCode) {
            this.bindSearchLocation(appId, appCode);
        };
        this.bindSearchLocation = function (appId, appCode) {
            $('#page_get_location').click(function (e) {
                e.preventDefault();
                let address = '';
                $('#page_addresss_fieldset input, #page_addresss_fieldset select').each(function () {
                    if ($(this).prop('id') !== "page_phone" && $(this).prop('id') !== "page_website") {
                        if ($(this).val() !== '') {
                            if ($(this).prop('id') === "page_country_id") {
                                address = address + $(this).find('option:selected').text() + ',';
                            } else if ($(this).prop('id') === "page_region_id") {
                                address = address + $(this).find('option:selected').text() + ',';
                            } else {
                                address = address + $(this).val() + ',';
                            }
                        }
                    }
                });
                address = encodeURIComponent(address);
                let url = 'https://geocoder.api.here.com/6.2/geocode.json?app_id=' + appId + '&app_code=' + appCode + '  &searchtext=' + address;
                $.get(url, function (data) {
                    if (typeof(data.Response) !== "undefined") {
                        let lat = data.Response.View[0].Result[0].Location.NavigationPosition[0].Latitude;
                        let lng = data.Response.View[0].Result[0].Location.NavigationPosition[0].Longitude;
                        $('#page_lat').val(lat);
                        $('#page_lng').val(lng);
                    }
                })
            });
        }
    };
    return StoreLocator;
});