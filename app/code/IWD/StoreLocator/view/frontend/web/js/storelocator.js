define([
    'jquery',
    'mage/template',
    'text!./templates/result.html',
    'text!./templates/result-popup.html',
    // 'jquery/ui',
    'IWD_StoreLocator/js/jquery.visible.min',
    'IWD_StoreLocator/js/infobox',
    'IWD_StoreLocator/js/markerclusterer_compiled'

], function ($, mageTemplate, resultTmpl, resultPopup) {
    "use strict";
    $.widget('mage.StoreLocator', {
        options: {
            url: '',
            baseUrlImage: '',
            template: resultTmpl,
            templatePopup: resultPopup,
            //pageSize: 5,
            currentPage: 1,
            resultContainer: "[data-role='result']",
            lat: '',
            lng: '',
            searchOnload: '',
            api_type: '',
            app_id: '',
            app_code: '',
            markerUrl: '',
            radiusDecorator: {},
            pagination: '',
            dropdown: {},

            metric: '',
            placeholder_visability: '',
            empty_message: '',
            init_here: ''

        },
        load: false,
        result: null,
        over: false,
        map: null,
        infoBubble: null,
        markers: [],
        decorateRadius: false,
        onlyByLocation: 0,
        currentIndex: null,
        hereUi: null,
        hereMap: null,
        platform: null,
        defaultLayers: null,
        hereMarkersGroup: null,
        behavior: null,

        _create: function () {
            if(this.options.init_here === true){
                this.initHereMap();
            }
            this._bind();
            this._detectLocation();
            this._scrollToTop();
        },
        initHereMap: function () {
            this.platform = new H.service.Platform({
                app_id: this.options.app_id,
                app_code: this.options.app_code,
                useHTTPS: true
            });
            this.defaultLayers = this.platform.createDefaultLayers();
            this.hereMap = new H.Map(
                document.getElementById('storelocator-map'),
                this.defaultLayers.normal.map,
                {
                    zoom: 3,
                    center: {lng: 25.2, lat: 133.77}
                }
            );
            this.behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(this.hereMap));
        },

        _scrollToTop: function () {
            $(".storelocator-back a").click(function (e) {
                e.preventDefault();
                $("html, body").animate({scrollTop: 0}, "slow");
            });
        },

        _bind: function () {
            var self = this;
            this._on({
                'click #storelocator_submit': function () {
                    this.over = false;
                    this.decorateRadius = true;
                    this.options.currentPage = 1;
                    if (this.options.api_type === 'google') {
                        this._reset();
                    }
                    this.onlyByLocation = 0;
                }
            });

            this._on({'click #storelocator_submit': $.proxy(this._search, this)});


            $(window).scroll(function () {

                var status = $('.storelocator-back').visible();

                if (status == true && self.load == false && self.options.searchOnload == 1) {
                    self.decorateRadius = false;
                    self._search();
                }
            });

            this._on({'change #country_field': $.proxy(this._changeRegion, this)});

            this._bindMobile();
        },

        _search: function () {

            var self = this;
            self.options.searchOnload = 1;

            if (self.over == true) {
                return;
            }

            self.load = true;

            var params = {
                "searchCriteria": {
                    "filter_groups": [
                        {
                            "filters": [
                                {
                                    "field": "lat",
                                    "value": self.options.lat,
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "lng",
                                    "value": self.options.lng,
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "country_id",
                                    "value": $('#country_field').val(),
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "store_id",
                                    "value": $('#store_field').val(),
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "region_id",
                                    "value": $('#region_id_field').val(),
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "region",
                                    "value": $('#region_field').val(),
                                    "condition_type": "eq"
                                },
                                {
                                    "field": "distance",
                                    "value": $('#radius_field').val(),
                                    "condition_type": "eq"
                                },

                                {
                                    "field": "onlyLocation",
                                    "value": self.onlyByLocation,
                                    "condition_type": "eq"
                                }

                            ]
                        }
                    ],
                    "current_page": self.options.currentPage,
                    "page_size": self.options.pageSize,

                }
            };

            if (self.options.currentPage > 1 && self.options.pagination != 1) {
                return;
            }
            $.ajax({
                url: self.options.url,
                dataType: 'json',
                data: params,
                context: $('body'),
                showLoader: true
            }).done(function (data) {
                self._drawResultTable(data);
                self.load = false;
            }).fail(function (response) {
                var msg = $("<div/>").addClass("message notice").text(response.responseJSON.message);
                this.find(self.options.messagesSelector).prepend(msg);
                self.load = false;
            });
        },

        //render store item
        _drawResultTable: function (data) {
            //this.clearMarkers();
            var tmpl = mageTemplate(this.options.template);

            tmpl = tmpl({
                data: data,
                url: this.options.baseUrlImage,
                placeholder: this.options.placeholder,
                metric: this.options.metric,
                placeholder_visability: this.options.placeholder_visability,
                empty_message: this.options.empty_message
            });

            if (this.options.currentPage == 1) {
                this.element.find(this.options.resultContainer).html($(tmpl));
            } else {
                this.element.find(this.options.resultContainer).append($(tmpl));
            }
            if (this.options.currentPage * data.search_criteria.page_size >= data.total_count) {
                this.over = true;
            }

            $('.storelocator-wrapper .header').html($.mage.__('We found') + ' ' + data.total_count + ' ' + $.mage.__('locations!'))


            this.options.currentPage = this.options.currentPage + 1;
            this.result = data.total_count;
            if (this.options.api_type === 'google') {
                this._applyMarker(data);
            }
            else {
                this._applyHereMarker(data);
            }

            var radius = $('#radius_field').val();
            if (this.options.metric == 'Miles') {
                radius = (radius / 0.621371);
            }
            radius = radius * 1000;
            this._applyRadius(data.search_criteria.filter_groups[0].filters, radius, data);

            if ($('.storelocator-item').length > 0) {
                $('.storelocator-back').show();
            } else {
                $('.storelocator-back').hide();
            }
        },

        _drawResultPopup: function (data) {
            let tmpl = mageTemplate(this.options.templatePopup);
            tmpl = tmpl({data: data, url: this.options.baseUrlImage, metric: this.options.metric,});
            return '<div class="infoBox-inner">' + tmpl + '</div>';
        },

        //check location by default browser logic
        _detectLocation: function () {
            let self = this;
            if (location.protocol === 'https:') {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        self.options.lat = position.coords.latitude;
                        self.options.lng = position.coords.longitude;

                        if (self.options.searchOnload === 1) {
                            self._search();
                        }
                    });
                }
            }
        },
        _reset: function () {
            this.clearMarkers();
            bounds = new google.maps.LatLngBounds();//reset bound for correct fit map
        },

        addMarkerToGroup: function (coordinate, html) {
            let marker = new H.map.Marker(coordinate);
            // add custom data to the marker
            marker.setData(html);
            this.hereMarkersGroup.addObject(marker);
        },
        _applyHereMarker: function (data) {
            let self = this;
            this.hereMarkersGroup = new H.map.Group();
            try {
                if (this.hereMap.getObjects().length !== 0) {
                    this.hereMap.removeObjects(this.hereMap.getObjects());
                    this.hereMap.removeObjects(this.hereMarkersGroup.getObjects())
                }
            } catch (e) {
                this.hereMap.removeObjects(this.hereMap.getObjects());
                this.hereMap.removeObjects(this.hereMarkersGroup.getObjects())
            }
            if (data.items.length !== 0) {
                this.hereMap.addObject(this.hereMarkersGroup);
                let ui = H.ui.UI.createDefault(this.hereMap, this.defaultLayers);
                this.hereMarkersGroup.addEventListener('tap', function (evt) {
                    // event target is the marker itself, group is a parent event target
                    // for all objects that it contains
                    let bubble = new H.ui.InfoBubble(evt.target.getPosition(), {
                        // read custom data
                        content: evt.target.getData()
                    });
                    // show info bubble

                    bubble.open();
                    ui.addBubble(bubble);
                }, false);

                $(data.items).each(function (index, item) {
                    let coordinates = {lat: item.lat, lng: item.lng};
                    let contentTemplate = self._drawResultPopup(item);
                    self.addMarkerToGroup(coordinates, contentTemplate);
                });
                this.hereMap.setViewBounds(this.hereMarkersGroup.getBounds());
            }
        },

        //create marker
        _applyMarker: function (data) {

            let self = this;
            let count = infoWindow.length;

            $(data.items).each(function (index, item) {
                var myLatlng = new google.maps.LatLng(item.lat, item.lng);
                bounds.extend(myLatlng);
                let marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    title: item.name,
                    icon: self.options.marker,

                });
                self.markers.push(marker);

                let contentTemplate = self._drawResultPopup(item);
                var indexMarker = count + index;
                infoWindow[indexMarker] = new InfoBox({
                    content: contentTemplate,
                    disableAutoPan: false,
                    maxWidth: 279,
                    pixelOffset: new google.maps.Size(20, -143),
                    zIndex: null,
                    boxStyle: {
                        background: "none",
                        opacity: 1,
                        width: "279px",
                        top: "-10px"
                    },
                    closeBoxMargin: "0 0 0 0",
                    closeBoxURL: self.options.closeUrl,
                    infoBoxClearance: new google.maps.Size(1, 1)
                });

                google.maps.event.addListener(marker, 'click', function () {
                    if (self.currentIndex != null) {
                        infoWindow[self.currentIndex].close();
                    }
                    infoWindow[indexMarker].open(map, this);
                    self.currentIndex = indexMarker;
                    map.setCenter(infoWindow[indexMarker].getPosition());
                });

                map.fitBounds(bounds);
                map.panToBounds(bounds);


            });
        },

        //remove all markers+radius
        clearMarkers: function () {

            for (var i = 0; i < infoWindow.length; i++) {
                infoWindow[i].close();
            }

            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
            this.markers = [];
        },


        // render radius zone
        _applyRadius: function (filter, radius, data) {
            var lat = 0,
                lng = 0;

            $.each(filter, function (index, item) {
                if (item.field == 'lat') {
                    lat = item.value;
                }
                if (item.field == 'lng') {
                    lng = item.value;
                }
            });
            var self = this;
            if (data.total_count == 0) {
                var iconUrl = '';
                var title = '';
            } else {
                var iconUrl = self.options.markerUrl;
            }
            if (this.options.api_type === 'google') {
                var marker = new google.maps.Marker({
                    map: map,
                    position: new google.maps.LatLng(lat, lng),
                    title: 'Current Location',
                    icon: iconUrl
                });

                var myLatlng = new google.maps.LatLng(lat, lng);
                bounds.extend(myLatlng);
                map.fitBounds(bounds);
                map.panToBounds(bounds);

                if (this.options.radiusDecorator.active == 1 && this.decorateRadius == true) {
                    var circle = new google.maps.Circle({
                        map: map,
                        radius: radius,
                        fillColor: self.options.radiusDecorator.fillColor,
                        fillOpacity: self.options.radiusDecorator.fillOpacity,
                        strokeColor: self.options.radiusDecorator.strokeColor,
                        strokeOpacity: self.options.radiusDecorator.strokeOpacity,
                        strokeWeight: self.options.radiusDecorator.strokeWeight
                    });
                    circle.bindTo('center', marker, 'position');
                    self.markers.push(circle);
                }
                self.markers.push(marker);

                if (data.total_count == 0) {
                    map.setZoom(3);//zoom map
                    this._reset();
                }
            }
            else {
                if (data.total_count != 0) {
                    this.hereMap.addObject(new H.map.Circle(
                        // The central point of the circle
                        {lat: lat, lng: lng},
                        // The radius of the circle in meters
                        radius,
                        {
                            style: {
                                strokeColor: 'rgba(55, 85, 170, 0.6)', // Color of the perimeter
                                lineWidth: 2,
                                fillColor: 'rgba(0, 128, 0, 0.4)'  // Color of the circle
                            }
                        }
                    ));
                }
            }


        },

        _changeRegion: function () {
            return;
            var self = this;
            var countryId = $('#country_field').val();
            if (countryId == '') {
                $('#region_id_field').hide().val('');
                $('#region_id_field').find('option').remove();
                $('#region_field').show().val('');
                return;
            }
            $.each(self.options.dropdown, function (index, item) {
                if (index == countryId) {
                    if ($.isEmptyObject(item.regions)) {

                        $('#region_id_field').hide().val('');
                        $('#region_field').show();
                    } else {
                        $('#region_field').hide().val('');
                        $('#region_id_field').show();
                        $('#region_id_field').find('option').remove()

                        $('#region_id_field')
                            .append($("<option></option>")
                                .attr("value", '')
                                .text(''));

                        $.each(item.regions, function (label, id) {
                            $('#region_id_field')
                                .append($("<option></option>")
                                    .attr("value", id)
                                    .text(label));
                        });
                    }
                }
            });

        },

        _bindMobile: function () {
            var self = this;
            $('.storelcoator-mobile-function a').click(function (e) {
                e.preventDefault();
                self.onlyByLocation = 1;
                self._search();
            })
        }
        //end

    });

    return $.mage.StoreLocator;
});