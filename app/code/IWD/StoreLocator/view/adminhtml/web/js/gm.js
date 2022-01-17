define([
    "jquery"
], function($){
    'use strict';

    var StoreLocator = function(){

        this.init = function(key){
            this.bindSearchLocation(key);
        };
        
        this.bindSearchLocation = function(key){
            
            $('#page_get_location').click(function(e){
                                
                e.preventDefault();

                var address = '';
                var country = encodeURIComponent($('#page_addresss_fieldset select#page_country_id').val());
                
                $('#page_addresss_fieldset input, #page_addresss_fieldset select').each(function(){
                    
                    if ($(this).prop('id') !="page_phone" && $(this).prop('id') !="page_website"){
                        if ($(this).val()!=''){
                            if ($(this).prop('id') == "page_country_id"){
                                address = address + $(this).find('option:selected').text() + ',';
                            }else if ($(this).prop('id') == "page_region_id"){
                                address = address + $(this).find('option:selected').text() + ',';
                            }else{
                                address = address + $(this).val() + ',';
                            }
                        }
                    }
                });

                address = encodeURIComponent(address);
                var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + address+ '&components:' + country + '&key=' + key;
                $.get(url,function(data){
                    if (typeof(data.status)!="undefined"){
                        if (data.status=='OK'){
                            var lat = data.results[0].geometry.location.lat;
                            $('#page_lat').val(lat);
                            var lng = data.results[0].geometry.location.lng;
                            $('#page_lng').val(lng);
                        }
                    }
                })
                
            });
        }
    };
    
    return StoreLocator;
});