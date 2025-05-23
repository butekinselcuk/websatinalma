
var NosiLeafletMap = function (element, settings) {

    this.container = document.getElementById(element);
    this.map_id = "";
    this.region_searchbox = false;
    this.map_object = null;
    this.map_center_lat = 41.015137
    this.map_center_long = 28.979530;
    this.map_zoom = 17;
    this.marker_array = [];
    this.marker_first_latlong_array = [];
    this.polyline_array = [];
    this.drawnItems = false;
    this.DrawEvent = null;
    this.drawfeatureGroup;
    this.region_area_polyline_array = [];
    this.region_area_polygon_array = [];
    this.featureGroup = new L.featureGroup();
    this.conditionalLayer = false;
    this.circleMarkers = false;
    this.conditionalLayer_object;

    this.marker_current_location = null;
    this.oms_array = [];

    this.cluster_array = [];


    this.region_color = 'red';
    this.region_weight = 1;
    this.region_opacity = 1.0;
    this.region_smoothFactor = 1;
    this.region_pointList = [];
    this.region_code = "";
    this.region_dashArray = 0;
    this.region_map_object_name = 'map';

    this.regionbox_top = 10;

    this.current_point = false;
    this.layer_control = true;


    this.contextmenu = false;
    this.contextmenuWidth = 140;
    this.contextmenuItems = [];

    this.available_maps = ['osm'];
    this.maps = {
        "osm": { "name": "Open Street Maps", "url": "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", "subD": ['a', 'b', 'c'], "maxZoom": 20, "isElliptical": false }
    };

    if (this.container != null) {

        if (settings) {
            if (settings.map_id)
                this.map_id = settings.map_id;
            if (settings.map_center_lat)
                this.map_center_lat = settings.map_center_lat;
            if (settings.map_center_long)
                this.map_center_long = settings.map_center_long;
            if (settings.map_zoom)
                this.map_zoom = settings.map_zoom;
            if (settings.drawnItems)
                this.drawnItems = settings.drawnItems;
            if (settings.DrawEvent)
                this.DrawEvent = settings.DrawEvent;
            if (settings.region_searchbox)
                this.region_searchbox = settings.region_searchbox;
            if (settings.region_map_object_name)
                this.region_map_object_name = settings.region_map_object_name;
            if (settings.regionbox_top)
                this.regionbox_top = settings.regionbox_top;
            if (settings.current_point) 
                this.current_point = settings.current_point;

            if (settings.layer_control == false)
                this.layer_control = settings.layer_control;

            if (settings.contextmenu == true)
                this.contextmenu = settings.contextmenu;
            if (settings.contextmenuWidth>0)
                this.contextmenuWidth = settings.contextmenuWidth;
            if (settings.contextmenuItems != null)
                this.contextmenuItems = settings.contextmenuItems;
            if (settings.conditionalLayer)
                this.conditionalLayer = settings.conditionalLayer;
            if (settings.circleMarkers)
                this.circleMarkers = settings.circleMarkers;
            
        }
    }

    var contextmenu = this.contextmenu;
    var contextmenuWidth = this.contextmenuWidth;
    var contextmenuItems = this.contextmenuItems;
    var conditionalLayer = this.conditionalLayer;
    var circleMarkers = this.circleMarkers;
    var conditionalLayer_object = this.conditionalLayer_object;
    
    var container = this.container;
    var map_id = this.map_id;
    var map_object = this.map_object;
    var map_center_lat = this.map_center_lat;
    var map_center_long = this.map_center_long;
    var marker_array = this.marker_array;
    var available_maps = this.available_maps;
    var maps = this.maps;
    var polyline_array = this.polyline_array;
    var region_area_polyline_array = this.region_area_polyline_array;
    var region_area_polygon_array = this.region_area_polygon_array;
    
    var map_zoom = this.map_zoom;
    var marker_first_latlong_array = this.marker_first_latlong_array;
    var drawnItems = this.drawnItems;
    var DrawEvent = this.DrawEvent;
    var drawfeatureGroup = this.drawfeatureGroup;
    var featureGroup = this.featureGroup;
    var region_searchbox = this.region_searchbox;
    var region_map_object_name = this.region_map_object_name;
    var regionbox_top = this.regionbox_top;
    var current_point = this.current_point;
    var layer_control = this.layer_control;

    var marker_current_location = this.marker_current_location;
    var oms_array = this.oms_array;
    var cluster_array = this.cluster_array;

    var osmLayer = L.tileLayer(maps[available_maps[0]]["url"], {
        maxZoom: maps[available_maps[0]]["maxZoom"],
        subdomains: maps[available_maps[0]]["subD"],
        isElliptical: maps[available_maps[0]]["isElliptical"]
    });


    this.load_map = function () {
        if (map_object != null) map_object.remove();

        map_object = L.map(map_id, {
            attributionControl: false,
            contextmenu: contextmenu, 
            contextmenuWidth: contextmenuWidth,
            contextmenuItems: contextmenuItems
        });
        this.map_object = map_object;

        osmLayer.addTo(map_object);

        if (layer_control) {

            //L.control.layers({
            //    'Open Street Map': osmLayer,
            //    'Google Yol': googleRoadmap,
            //    'Google Uydu': googleSatellite,
            //    'Google Hybrid': googleHybrid,
            //    'Google Arazi': googleTerrain,
            //    'Yandex Yol': yandexRoadmap,
            //    'Yandex Uydu': yandexSatellite
            //}, null, { position: 'topleft' }).addTo(map_object);


        }

        map_object.on('baselayerchange', function (e) {
            var center = map_object.getCenter();
            var zoom = map_object.getZoom();
            map_object.options.crs = e.layer.options.isElliptical ? L.CRS.EPSG3395 : L.CRS.EPSG3857;
            map_object._resetView(center, zoom, false, false);
        });

        map_object.setView(new L.LatLng(map_center_lat, map_center_long), map_zoom);



        if (current_point)
        {
            var currentPointLocation = L.easyButton({
                states: [{
                    stateName: 'current-point',
                    icon: 'fa-map-marker',
                    title: 'Konumum',
                    onClick: function (control) {
                        try {

                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function (position) {

                                    map.add_currentlocationmarker({
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude,
                                        draggable: false,
                                        popup: ''
                                    });
                                    map_object.setView(new L.LatLng(position.coords.latitude, position.coords.longitude), 15);
                                }, function (error) {
                                    switch (error.code) {
                                        case error.PERMISSION_DENIED:
                                            alert("User denied the request for Geolocation.");
                                            break;
                                        case error.POSITION_UNAVAILABLE:
                                            alert("Location information is unavailable.");
                                            break;
                                        case error.TIMEOUT:
                                            alert("The request to get user location timed out.");
                                            break;
                                        case error.UNKNOWN_ERROR:
                                            alert("An unknown error occurred.");
                                            break;
                                    }
                                });
                            } else {
                                alert("Geolocation is not supported by this browser.");
                            }
                        } catch (err) {
                            alert(err.message);
                        }
                    }
                }]
            });
            currentPointLocation.addTo(map_object);
        }

        if (drawnItems)
        {
            drawfeatureGroup = L.featureGroup();
            map_object.addLayer(drawfeatureGroup);
            //L.control.layers({ 'drawlayer': drawn_items }, { position: 'topleft', collapsed: false }).addTo(map_object);
            map_object.addControl(new L.Control.Draw({
                edit: {
                    featureGroup: drawfeatureGroup,
                    poly: {
                        allowIntersection: false
                    }
                },
                draw: {
                    marker: false,
                    circle: false,
                    polyline: false,
                    polygon: {
                        allowIntersection: false,
                        showArea: true
                    }
                }
            }));

            map_object.on(L.Draw.Event.CREATED, function (event) {
                var layer = event.layer;

                drawfeatureGroup.addLayer(layer);
                

                if (DrawEvent != null) {
                    DrawEvent(layer);
                }
            });
            map_object.on(L.Draw.Event.EDITED, function (e) {

                var layers = e.layers;
                layers.eachLayer(function (layer) {
                    if (layer instanceof L.Polygon) {
                        //Do marker specific actions here
                        if (DrawEvent != null) {
                            DrawEvent(layer);
                        }
                    }
                });
            });

        }


        if (region_searchbox)
        {
            var searchbox_html = "<div class=\"searchbox_content\">" +
                                 "   <div>" +
                                 "       <input id=\"txtRegionSearchBox\" name=\"txtRegionSearchBox\" type=\"text\" class=\"bg-white-border-blue ui-autocomplete-input\" style=\"width:300px;\" value=\"\" placeholder=\"İl / İlçe..\" autocomplete=\"off\" role=\"textbox\" aria-autocomplete=\"list\" aria-haspopup=\"true\" >" +
                                 "   </div>" +
                                 "</div>" +
                                "<script>" +
                                "$(\".searchbox_content\").css(\"top\", \"" + regionbox_top + "px\"); " +
                                "$(\"#txtRegionSearchBox\").autocomplete({ " +
                                "    minLength:3, " +
                                "    source: function (request, response) { " +
                                "        $.ajax({ " +
                                "            url: \'/AutoComplete/AutoCompleteRegionArea\', global: false, " +
                                "            dataType: \"json\", " +
                                "            contentType: 'application/json, charset=utf-8', " +
                                "            data: { " +
                                "                prm: $(\"#txtRegionSearchBox\").val() " +
                                "            }, " +
                                "            success: function (data) { " +
                                "                response($.map(data, function (item) { " +
                                "                    return { " +
                                "                        label: item.Description, " +
                                "                        Value: item.Place_id " +
                                "                    }; " +
                                "                })); " +
                                "            }, " +
                                "            error: function (xhr, status, error) { " +
                                "                alert(error); " +
                                "            } " +
                                "        }); " +
                                "    }, select: function (event, ui) { " +
                                "        if(ui.item.Value!=\"\") { " +
                                "           ReturnRegionAreaGeocodes(" + region_map_object_name + ",ui.item.Value,ui.item.label); " +
                                "        } " +
                                "    } " +
                                "}); " +
                                "</script>";

            //$("#" + map_id).append(searchbox_html);
            
            $('#' + map_id).after(searchbox_html);


        }

    }


    this.add_circle_marker = function (_settings) {
        if (map_object != null) {
            var draggable = false;
            var lat;
            var lng;
            var popup = "";
            var number = "";
            var className = "";
            var areaIcon = false;
            var iconUrl = "/leaflet/images/marker-icon.png";
            var tooltip = "";
            var unique_code = "";
            var drag = null;
            var dragstart = null;
            var dragend = null;
            var click = null;
            var unique_id = 0;
            var borderColor = "";
            var marker_type = "0";
            var opacity = 1;
            //var prm1 = "0";
            //var prm2 = "0";
            //var prm3 = "0";
            //var prm4 = "0";
            //var prm5 = "0";
            //var zs = "0";
            var route = "";
            var route_index = 0;
            var isWidth = 25;
            var isHeight = 41;
            var iaWidth = 13;
            var iaHeight = 41;
            var paWidth = 0;
            var paHeight = -33;
            var oms = null;
            var clusterColor = "";
            var ClusterMarkerCount = "";
            var regionID = "";
            var special_parameter = 0;
            var markerColor = "#000";
            var iconMarker = true;
            var cluster = false;

            var prm_object = null;

            if (_settings.iconMarker == false) {
                iconMarker = _settings.iconMarker;
                console.log(_settings.iconMarker);
            }

            if (_settings.lat) lat = _settings.lat;
            if (_settings.lng) lng = _settings.lng;
            if (_settings.popup) popup = _settings.popup;
            if (_settings.draggable) draggable = _settings.draggable;
            if (_settings.number) number = _settings.number;
            if (_settings.ClusterMarkerCount) ClusterMarkerCount = _settings.ClusterMarkerCount;
            if (_settings.className) className = _settings.className;
            if (_settings.borderColor) borderColor = _settings.borderColor;
            if (_settings.iconUrl) iconUrl = _settings.iconUrl;
            if (_settings.tooltip) tooltip = _settings.tooltip;
            if (_settings.unique_code) unique_code = _settings.unique_code;
            if (_settings.unique_id) unique_id = _settings.unique_id;
            if (_settings.drag) drag = _settings.drag;
            if (_settings.dragstart) dragstart = _settings.dragstart;
            if (_settings.dragend) dragend = _settings.dragend;
            if (_settings.click) click = _settings.click;
            if (_settings.marker_type) marker_type = _settings.marker_type;
            if (_settings.areaIcon) areaIcon = _settings.areaIcon;
            //if (_settings.prm1) prm1 = _settings.prm1;
            //if (_settings.prm2) prm2 = _settings.prm2;
            //if (_settings.prm3) prm3 = _settings.prm3;
            //if (_settings.prm4) prm4 = _settings.prm4;
            //if (_settings.prm5) prm5 = _settings.prm5;
            //if (_settings.zs) zs = _settings.zs;

            if (_settings.prm_object != null) prm_object = _settings.prm_object;

            if (_settings.route) route = _settings.route;
            if (_settings.route_index) route_index = _settings.route_index;
            if (_settings.opacity) opacity = _settings.opacity;
            if (_settings.isWidth) {
                isWidth = _settings.isWidth;
                iaWidth = isWidth / 2;
            }
            if (_settings.isHeight) {
                isHeight = _settings.isHeight;
                iaHeight = isHeight;
                paHeight = 8 - isHeight;
            }
            if (_settings.regionID) regionID = _settings.regionID;

            //if (_settings.iaWidth) iaWidth = _settings.iaWidth;
            //if (_settings.iaHeight) iaHeight = _settings.iaHeight;
            if (_settings.paWidth) paWidth = _settings.paWidth;
            //if (_settings.paHeight) paHeight = _settings.paHeight;
            if (_settings.oms) oms = _settings.oms;

            if (_settings.clusterColor) clusterColor = _settings.clusterColor;
            if (_settings.special_parameter) special_parameter = _settings.special_parameter;

            if (_settings.markerColor) markerColor = _settings.markerColor;

            //var _icon = null;
            //if (number != "") {
            //    _icon = new NumberedDivIcon({ iconUrl: iconUrl, number: number });
            //}
            //else if (ClusterMarkerCount != "") {
            //    _icon = new ClusterMarkerDivIcon({ className: className, backgroundColor: clusterColor, number: ClusterMarkerCount });
            //}
            //else if (className != "") {
            //    _icon = new ContentDivIcon({ className: className, borderColor: borderColor });
            //}
            //else if (areaIcon) {
            //    _icon = new AreaDivIcon({ iconUrl: iconUrl });
            //}
            //else {
            //    _icon = new L.icon({
            //        iconUrl: iconUrl, iconSize: new L.Point(isWidth, isHeight),
            //        iconAnchor: new L.Point(iaWidth, iaHeight),
            //        popupAnchor: new L.Point(paWidth, paHeight),
            //    });
            //}


            var circleMarker = L.circleMarker([lat, lng], {
                radius: 3,
                fillColor: markerColor,
                color: "#000",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.9,
                draggable: draggable, opacity: opacity, special_parameter: special_parameter, unique_id: unique_id, unique_code: unique_code
            });

            if (popup != "") circleMarker.bindPopup(popup, { maxWidth: 900 });
            if (tooltip != "") circleMarker.bindTooltip(tooltip, { permanent: true, className: "", offset: [0, 0] });

            if (drag != null) {
                circleMarker.on('drag', function (e) {
                    //console.log('marker drag event');
                    drag(e);
                });
            }

            if (dragstart != null) {
                circleMarker.on('dragstart', function (e) {
                    //console.log('marker dragstart event');
                    dragstart(e);
                });
            }

            if (dragend != null) {
                circleMarker.on('dragend', function (e) {
                    //console.log('marker dragend event');
                    dragend(e);
                });
            }

            if (click != null) {
                circleMarker.on('click', function (e) {
                    //console.log('marker dragend event');
                    click(e);
                });
            }

            var marker = null;

            marker_array.push({ uniqueCode: unique_code, cluster: cluster, iconMarker: iconMarker, markerType: marker_type, uniqueId: unique_id, marker: marker, circleMarker: circleMarker, regionID: regionID, route: route, route_index: route_index, prm_object: prm_object });
            marker_first_latlong_array.push({ uniqueCode: unique_code, markerType: marker_type, uniqueId: unique_id, lat: lat, lng: lng, route: route, route_index: route_index, prm_object: prm_object });

            circleMarker.addTo(map_object);

        }
    }

    this.add_marker = function (_settings) {
        if (map_object != null) {
            var draggable = false;
            var lat;
            var lng;
            var popup = "";
            var number = "";
            var className = "";
            var areaIcon = false;
            var iconUrl = "/leaflet/images/marker-icon.png";
            var tooltip = "";
            var unique_code = "";
            var drag = null;
            var dragstart = null;
            var dragend = null;
            var click = null;
            var unique_id = 0;
            var borderColor = "";
            var marker_type = "0";
            var opacity = 1;
            //var prm1 = "0";
            //var prm2 = "0";
            //var prm3 = "0";
            //var prm4 = "0";
            //var prm5 = "0";
            //var zs = "0";
            var route = "";
            var route_index = 0;
            var isWidth = 25;
            var isHeight = 41;
            var iaWidth = 13;
            var iaHeight = 41;
            var paWidth = 0;
            var paHeight = -33;
            var oms = null;
            var clusterColor = "";
            var ClusterMarkerCount = "";
            var regionID = "";
            var special_parameter = 0;
            var markerColor = "#000";
            var iconMarker = true;
            var cluster = false;

            var prm_object = null;

            if (_settings.iconMarker==false) {
                iconMarker = _settings.iconMarker;
                console.log(_settings.iconMarker);
            }

            if (_settings.lat) lat = _settings.lat;
            if (_settings.lng) lng = _settings.lng;
            if (_settings.popup) popup = _settings.popup;
            if (_settings.draggable) draggable = _settings.draggable;
            if (_settings.number) number = _settings.number;
            if (_settings.ClusterMarkerCount) ClusterMarkerCount = _settings.ClusterMarkerCount;
            if (_settings.className) className = _settings.className;
            if (_settings.borderColor) borderColor = _settings.borderColor;
            if (_settings.iconUrl) iconUrl = _settings.iconUrl;
            if (_settings.tooltip) tooltip = _settings.tooltip;
            if (_settings.unique_code) unique_code = _settings.unique_code;
            if (_settings.unique_id) unique_id = _settings.unique_id;
            if (_settings.drag) drag = _settings.drag;
            if (_settings.dragstart) dragstart = _settings.dragstart;
            if (_settings.dragend) dragend = _settings.dragend;
            if (_settings.click) click = _settings.click;
            if (_settings.marker_type) marker_type = _settings.marker_type;
            if (_settings.areaIcon) areaIcon = _settings.areaIcon;
            //if (_settings.prm1) prm1 = _settings.prm1;
            //if (_settings.prm2) prm2 = _settings.prm2;
            //if (_settings.prm3) prm3 = _settings.prm3;
            //if (_settings.prm4) prm4 = _settings.prm4;
            //if (_settings.prm5) prm5 = _settings.prm5;
            //if (_settings.zs) zs = _settings.zs;

            if (_settings.prm_object != null) prm_object = _settings.prm_object;

            if (_settings.route) route = _settings.route;
            if (_settings.route_index) route_index = _settings.route_index;
            if (_settings.opacity) opacity = _settings.opacity;
            if (_settings.isWidth) {
                isWidth = _settings.isWidth;
                iaWidth = isWidth/2;
            }
            if (_settings.isHeight) {
                isHeight = _settings.isHeight;
                iaHeight = isHeight;
                paHeight = 8 - isHeight;
            }
            if (_settings.regionID) regionID = _settings.regionID;
            
            //if (_settings.iaWidth) iaWidth = _settings.iaWidth;
            //if (_settings.iaHeight) iaHeight = _settings.iaHeight;
            if (_settings.paWidth) paWidth = _settings.paWidth;
            //if (_settings.paHeight) paHeight = _settings.paHeight;
            if (_settings.oms) oms = _settings.oms;

            if (_settings.clusterColor) clusterColor = _settings.clusterColor;
            if (_settings.special_parameter) special_parameter = _settings.special_parameter;

            if (_settings.markerColor) markerColor = _settings.markerColor;
            
            var _icon = null;
            if (number != "") {
                _icon = new NumberedDivIcon({ iconUrl: iconUrl, number: number });
            }
            else if (ClusterMarkerCount != "") {
                _icon = new ClusterMarkerDivIcon({ className: className, backgroundColor: clusterColor, number: ClusterMarkerCount });
            }
            else if (className != "")
            {
                _icon = new ContentDivIcon({ className: className, borderColor: borderColor });
            }
            else if (areaIcon) {
                _icon = new AreaDivIcon({ iconUrl: iconUrl });
            }
            else {
                _icon = new L.icon({
                    iconUrl: iconUrl, iconSize: new L.Point(isWidth, isHeight),
                    iconAnchor: new L.Point(iaWidth, iaHeight),
                    popupAnchor: new L.Point(paWidth, paHeight),
                });
            }

            var marker = L.marker([lat, lng], { icon: _icon, draggable: draggable, opacity: opacity, special_parameter: special_parameter, unique_id: unique_id, unique_code: unique_code });
            if (popup != "") marker.bindPopup(popup, { maxWidth:900 });
            if (tooltip != "") marker.bindTooltip(tooltip, { permanent: true, className: "", offset: [0, 0] });
            
            if (drag != null) {
                marker.on('drag', function (e) {
                    //console.log('marker drag event');
                    drag(e);
                });
            }

            if (dragstart != null) {
                marker.on('dragstart', function (e) {
                    //console.log('marker dragstart event');
                    dragstart(e);
                });
            }

            if (dragend != null) {
                marker.on('dragend', function (e) {
                    //console.log('marker dragend event');
                    dragend(e);
                });
            }

            if (click != null) {
                marker.on('click', function (e) {
                    //console.log('marker dragend event');
                    click(e);
                });
            }


            var circleMarker = null;

            if (circleMarkers == true) {
                circleMarker = L.circleMarker([lat, lng], {
                    radius: 8,
                    fillColor: markerColor,
                    color: "#000",
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                });
            }

            marker_array.push({ uniqueCode: unique_code, cluster: cluster, iconMarker: iconMarker, markerType: marker_type, uniqueId: unique_id, marker: marker, circleMarker: circleMarker, regionID: regionID, route: route, route_index: route_index, prm_object: prm_object });
            marker_first_latlong_array.push({ uniqueCode: unique_code, markerType: marker_type, uniqueId: unique_id, lat: lat, lng: lng, route: route, route_index: route_index, prm_object: prm_object });

            if (circleMarkers == true)
            {
                circleMarker.addTo(map_object);
            }

            if (conditionalLayer == true) {

            }
            else {
                marker.addTo(map_object);
            }

            
            var oms_cnt = 0;
            for (var i = 0; i < oms_array.length; i++) {
                if (oms_array[i].latitude == lat && oms_array[i].longitude == lng) {
                    var _oms = oms_array[i].oms;
                    _oms.addMarker(marker);
                    _oms.addListener('spiderfy', function (markers) {
                        map_object.closePopup();
                    });
                    oms_cnt = 1;
                    break;
                }
            }

            if (oms_cnt == 0) {
                var _oms = new OverlappingMarkerSpiderfier(map_object, { keepSpiderfied: true });
                _oms.addMarker(marker);
                _oms.addListener('spiderfy', function (markers) {
                    map_object.closePopup();
                });
                oms_array.push({ latitude: lat, longitude: lng, oms: _oms });
            }

            if (circleMarkers != true) {
                featureGroup.addLayer(marker);
            }
        }
    }

    this.add_marker_by_cluster = function (_settings) {
        if (map_object != null) {

            var draggable = false;
            var lat;
            var lng;
            var popup = "";
            var number = "";
            var className = "";
            var areaIcon = false;
            var iconUrl = "/leaflet/images/marker-icon.png";
            var tooltip = "";
            var unique_code = "";
            var drag = null;
            var dragstart = null;
            var dragend = null;
            var click = null;
            var unique_id = 0;
            var borderColor = "";
            var marker_type = "0";
            var opacity = 1;
            //var prm1 = "0";
            //var prm2 = "0";
            //var prm3 = "0";
            //var prm4 = "0";
            //var prm5 = "0";
            //var zs = "0";
            var route = "";
            var route_index = 0;
            var isWidth = 25;
            var isHeight = 41;
            var iaWidth = 13;
            var iaHeight = 41;
            var paWidth = 0;
            var paHeight = -33;
            var oms = null;
            var clusterCode = "0";
            var ClusterColor = "";
            var regionID = "";
            var special_parameter = 0;
            var iconMarker = true;
            var cluster = true;

            var prm_object = null;

            if (_settings.iconMarker) iconMarker = _settings.iconMarker;
            if (_settings.lat) lat = _settings.lat;
            if (_settings.lng) lng = _settings.lng;
            if (_settings.popup) popup = _settings.popup;
            if (_settings.draggable) draggable = _settings.draggable;
            if (_settings.number) number = _settings.number;
            if (_settings.className) className = _settings.className;
            if (_settings.borderColor) borderColor = _settings.borderColor;
            if (_settings.iconUrl) iconUrl = _settings.iconUrl;
            if (_settings.tooltip) tooltip = _settings.tooltip;
            if (_settings.unique_code) unique_code = _settings.unique_code;
            if (_settings.unique_id) unique_id = _settings.unique_id;
            if (_settings.drag) drag = _settings.drag;
            if (_settings.dragstart) dragstart = _settings.dragstart;
            if (_settings.dragend) dragend = _settings.dragend;
            if (_settings.click) click = _settings.click;
            if (_settings.marker_type) marker_type = _settings.marker_type;
            if (_settings.areaIcon) areaIcon = _settings.areaIcon;
            //if (_settings.prm1) prm1 = _settings.prm1;
            //if (_settings.prm2) prm2 = _settings.prm2;
            //if (_settings.prm3) prm3 = _settings.prm3;
            //if (_settings.prm4) prm4 = _settings.prm4;
            //if (_settings.prm5) prm5 = _settings.prm5;
            //if (_settings.zs) zs = _settings.zs;
            if (_settings.regionID) regionID = _settings.regionID;
            if (_settings.route) route = _settings.route;
            if (_settings.route_index) route_index = _settings.route_index;
            if (_settings.opacity) opacity = _settings.opacity;
            if (_settings.isWidth) {
                isWidth = _settings.isWidth;
                iaWidth = isWidth / 2;
            }
            if (_settings.isHeight) {
                isHeight = _settings.isHeight;
                iaHeight = isHeight;
                paHeight = 8 - isHeight;
            }
            if (_settings.paWidth) paWidth = _settings.paWidth;
            if (_settings.oms) oms = _settings.oms;
            
            if (_settings.ClusterColor) ClusterColor = _settings.ClusterColor;
            if (_settings.special_parameter) special_parameter = _settings.special_parameter;

            if (_settings.prm_object != null) prm_object = _settings.prm_object;
            

            clusterCode = _settings.clusterCode;


            var _icon = null;
            if (number != "") {
                _icon = new NumberedDivIcon({ iconUrl: iconUrl, number: number });
            }
            else if (className != "") {
                _icon = new ContentDivIcon({ className: className, borderColor: borderColor });
            }
            else if (areaIcon) {
                _icon = new AreaDivIcon({ iconUrl: iconUrl });
            }
            else {
                _icon = new L.icon({
                    iconUrl: iconUrl, iconSize: new L.Point(isWidth, isHeight),
                    iconAnchor: new L.Point(iaWidth, iaHeight),
                    popupAnchor: new L.Point(paWidth, paHeight),
                });
            }

            var marker = L.marker([lat, lng], { icon: _icon, draggable: draggable, opacity: opacity, special_parameter: special_parameter });

            if (popup != "") marker.bindPopup(popup, { maxWidth: 800 });
            if (tooltip != "") marker.bindTooltip(tooltip, { permanent: true, className: "", offset: [0, 0] });

            if (drag != null) {
                marker.on('drag', function (e) {
                    //console.log('marker drag event');
                    drag(e);
                });
            }

            if (dragstart != null) {
                marker.on('dragstart', function (e) {
                    //console.log('marker dragstart event');
                    dragstart(e);
                });
            }

            if (dragend != null) {
                marker.on('dragend', function (e) {
                    //console.log('marker dragend event');
                    dragend(e);
                });
            }

            if (click != null) {
                marker.on('click', function (e) {
                    //console.log('marker dragend event');
                    click(e);
                });
            }
            
            var oms_cnt = 0;
            for (var i = 0; i < oms_array.length; i++) {
                if (oms_array[i].latitude == lat && oms_array[i].longitude == lng) {
                    var _oms = oms_array[i].oms;
                    _oms.addMarker(marker);
                    _oms.addListener('spiderfy', function (markers) {
                        map_object.closePopup();
                    });
                    oms_cnt = 1;
                    break;
                }
            }
            if (oms_cnt == 0) {
                var _oms = new OverlappingMarkerSpiderfier(map_object, { keepSpiderfied: true });
                _oms.addMarker(marker);
                _oms.addListener('spiderfy', function (markers) {
                    map_object.closePopup();
                });
                oms_array.push({ latitude: lat, longitude: lng, oms: _oms });
            }


            var cluster_group_cnt = 0;
            for (var i = 0; i < cluster_array.length; i++) {
                if (cluster_array[i].ClusterCode == clusterCode) {
                    var _ClusterGroup = cluster_array[i].ClusterGroup;
                    _ClusterGroup.addLayer(marker);
                    //marker_array.push({ ClusterGroup: _ClusterGroup, cluster: cluster, iconMarker: iconMarker, uniqueCode: unique_code, regionID: regionID, markerType: marker_type, uniqueId: unique_id, marker: marker, prm1: prm1, prm2: prm2, prm3: prm3, prm4: prm4, prm5: prm5, zs: zs, route: route, route_index: route_index });
                    marker_array.push({ ClusterGroup: _ClusterGroup, cluster: cluster, iconMarker: iconMarker, uniqueCode: unique_code, regionID: regionID, markerType: marker_type, uniqueId: unique_id, marker: marker, route: route, route_index: route_index, prm_object: prm_object });
                    cluster_group_cnt = 1;
                    break;
                }
            }
            if (cluster_group_cnt == 0) {
                var _ClusterGroup = new L.MarkerClusterGroup({
                    ClusterColor: ClusterColor
                });
                _ClusterGroup.addLayer(marker);

                //_ClusterGroup.on('clusterclick', function (c) {
                //    var popup = L.popup()
                //        .setLatLng(c.layer.getLatLng())
                //        .setContent(c.layer._childCount + ' Locations(click to Zoom)')
                //        .openOn(map_object);
                //});

                marker_array.push({ ClusterGroup: _ClusterGroup, cluster: cluster, iconMarker: iconMarker, uniqueCode: unique_code, regionID: regionID, markerType: marker_type, uniqueId: unique_id, marker: marker, route: route, route_index: route_index, prm_object: prm_object });
                cluster_array.push({ ClusterGroup: _ClusterGroup, ClusterCode: clusterCode });
                map_object.addLayer(_ClusterGroup);
            }
        }
    }

    this.add_path = function (_settings)
    {
        var color = 'red';
        var weight = 4;
        var opacity = 0.9;
        var smoothFactor = 1;
        var pointList = [];
        var unique_code = "";
        var dashArray = 0;

        if (_settings.color) color = _settings.color;
        if (_settings.weight) weight = _settings.weight;
        if (_settings.opacity) opacity = _settings.opacity;
        if (_settings.smoothFactor) smoothFactor = _settings.smoothFactor;
        if (_settings.pointList) pointList = _settings.pointList;
        if (_settings.unique_code) unique_code = _settings.unique_code;
        if (_settings.dashArray) dashArray = _settings.dashArray;

        var polyline = new L.Polyline(pointList, {
            color: color,
            weight: weight,
            opacity: opacity,
            smoothFactor: smoothFactor,
            dashArray: dashArray

        });
        polyline_array.push({ uniqueCode: unique_code, marker: polyline });
        polyline.addTo(map_object);
    }

    this.add_regionpath = function (_settings) {
        var region_color = 'red';
        var region_weight = 2;
        var region_opacity = 1.0;
        var region_smoothFactor = 1;
        var region_pointList = [];
        var region_code = "";
        var region_dashArray = 0;

        if (_settings.region_color) region_color = _settings.region_color;
        if (_settings.region_weight) region_weight = _settings.region_weight;
        if (_settings.region_opacity) region_opacity = _settings.region_opacity;
        if (_settings.region_smoothFactor) region_smoothFactor = _settings.region_smoothFactor;
        if (_settings.region_pointList) region_pointList = _settings.region_pointList;
        if (_settings.region_code) region_code = _settings.region_code;
        if (_settings.region_dashArray) region_dashArray = _settings.region_dashArray;

        var polyline = new L.Polyline(region_pointList, {
            color: region_color,
            weight: region_weight,
            opacity: region_opacity,
            smoothFactor: region_smoothFactor,
            dashArray: region_dashArray

        });
        region_area_polyline_array.push({ uniqueCode: region_code, marker: polyline });
        polyline.addTo(map_object);

    }

    this.add_regionpolygon = function (_settings) {
        var region_color = 'red';
        var region_weight = 2;
        var region_opacity = 1.0;
        var region_smoothFactor = 1;
        var region_pointList = [];
        var region_code = "";
        var region_dashArray = 0;



        if (_settings.region_color) region_color = _settings.region_color;
        if (_settings.region_weight) region_weight = _settings.region_weight;
        if (_settings.region_opacity) region_opacity = _settings.region_opacity;
        if (_settings.region_smoothFactor) region_smoothFactor = _settings.region_smoothFactor;
        if (_settings.region_pointList) region_pointList = _settings.region_pointList;
        if (_settings.region_code) region_code = _settings.region_code;
        if (_settings.region_dashArray) region_dashArray = _settings.region_dashArray;

        var polygon = L.polygon(region_pointLis, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5
        });

        region_area_polygon_array.push({ uniqueCode: region_code, marker: polygon });
        polygon.addTo(map_object);

    }

    this.remove_all_regionpath = function () {
        for (var i = 0; i < region_area_polyline_array.length; i++) {
            map_object.removeLayer(region_area_polyline_array[i].marker);
        }
        region_area_polyline_array = [];
        this.region_area_polyline_array = region_area_polyline_array;
    }

    this.remove_all_regionpolygon = function () {
        for (var i = 0; i < region_area_polygon_array.length; i++) {
            map_object.removeLayer(region_area_polygon_array[i].marker);
        }
        region_area_polygon_array = [];
        this.region_area_polygon_array = region_area_polygon_array;
    }

    this.remove_all_marker = function () {
        for (var i = 0; i < marker_array.length; i++) {
            try { map_object.removeLayer(marker_array[i].marker); } catch (err) { }
            try { map_object.removeLayer(marker_array[i].circleMarker); } catch (err) { }
        }
        marker_array = [];
        featureGroup = new L.featureGroup();
        this.featureGroup = featureGroup;
        
        this.marker_array = marker_array;
    }

    this.remove_all_marker_by_marker_type = function (marker_type) {

        var _marker_array = [];
        var _featureGroup = new L.featureGroup();
        for (var i = 0; i < marker_array.length; i++) {
            if (marker_array[i].markerType == marker_type) {
                try { map_object.removeLayer(marker_array[i].marker); } catch (err) { }
                try { map_object.removeLayer(marker_array[i].circleMarker); } catch (err) { }
            }
            else {
                _marker_array.push(marker_array[i]);
                _featureGroup.addLayer(marker_array[i].marker);
            }
        }

        marker_array = _marker_array;
        featureGroup = _featureGroup;
        this.featureGroup = featureGroup;
        this.marker_array = marker_array;
    }

    this.remove_all_marker_99 = function () {
        var marker_array_backup = [];
        for (var i = 0; i < marker_array.length; i++) {
            if (marker_array[i].markerType == "99") {
                try { map_object.removeLayer(marker_array[i].marker); } catch (err) { }
                try { map_object.removeLayer(marker_array[i].circleMarker); } catch (err) { }
            }
            else {
                marker_array_backup.push(marker_array[i]);
                //marker_array_backup.push({
                //    uniqueCode: marker_array[i].uniqueCode,
                //    markerType: marker_array[i].markerType,
                //    uniqueId: marker_array[i].uniqueId,
                //    marker: marker_array[i].marker,
                //    prm1: marker_array[i].prm1,
                //    prm2: marker_array[i].prm2,
                //    prm3: marker_array[i].prm3,
                //    prm4: marker_array[i].prm4,
                //    prm5: marker_array[i].prm5,
                //    zs: marker_array[i].zs,
                //    route: marker_array[i].route,
                //    route_index: marker_array[i].route_index
                //});
            }
        }
        marker_array = [];

        marker_array = marker_array_backup;

        featureGroup = new L.featureGroup();
        this.featureGroup = featureGroup;

        this.marker_array = marker_array;
    }
    
    this.remove_complainan_marker = function () {
        var marker_array_backup = [];
        for (var i = 0; i < marker_array.length; i++) {
            if (marker_array[i].uniqueCode == "ComplainantSortieSearchByCustomerMarker") {
                try { map_object.removeLayer(marker_array[i].marker); } catch (err) { }
            }
            else {
                marker_array_backup.push(marker_array[i]);
                //marker_array_backup.push({
                //    uniqueCode: marker_array[i].uniqueCode,
                //    markerType: marker_array[i].markerType,
                //    uniqueId: marker_array[i].uniqueId,
                //    marker: marker_array[i].marker,
                //    prm1: marker_array[i].prm1,
                //    prm2: marker_array[i].prm2,
                //    prm3: marker_array[i].prm3,
                //    prm4: marker_array[i].prm4,
                //    prm5: marker_array[i].prm5,
                //    zs: marker_array[i].zs,
                //    route: marker_array[i].route,
                //    route_index: marker_array[i].route_index
                //});
            }
        }
        marker_array = [];

        marker_array = marker_array_backup;

        featureGroup = new L.featureGroup();
        this.featureGroup = featureGroup;

        this.marker_array = marker_array;
    }

    this.remove_all_path = function () {
        for (var i = 0; i < polyline_array.length; i++) {
            map_object.removeLayer(polyline_array[i].marker);
        }
        polyline_array = [];
        this.polyline_array = polyline_array;
    }

    this.remove_all_path_by_unique_code = function (uniqueCode) {

        var _polyline_array = [];
        for (var i = 0; i < polyline_array.length; i++) {
            if (polyline_array[i].uniqueCode == uniqueCode) {
                map_object.removeLayer(polyline_array[i].marker);
            }
            else {
                _polyline_array.push(polyline_array[i]);
            }
        }

        polyline_array = _polyline_array;
        this.polyline_array = polyline_array;
    }

    this.remove_cluster = function () {
        for (var i = 0; i < marker_array.length; i++) {
            var cluster = marker_array[i].ClusterGroup;
            cluster.removeLayer(marker_array[i].marker);
        }
    }

    this.remove_cluster_by_marker_type = function (marker_type) {
        for (var i = 0; i < marker_array.length; i++) {
            if (marker_array[i].markerType == marker_type) {
                var cluster = marker_array[i].ClusterGroup;
                cluster.removeLayer(marker_array[i].marker);
            }
        }
    }

    this.set_default_markers = function () {

        for (var i = 0; i < marker_array.length; i++) {
            var a = marker_array[i];

            for (var j = 0; j < marker_first_latlong_array.length; j++) {
                var b = marker_first_latlong_array[j];
                if (b.uniqueId == a.uniqueId && b.markerType == "1" && a.markerType == "1")
                {
                    var LatLng = new L.LatLng(b.lat, b.lng);
                    marker_array[i].marker.setLatLng(LatLng);
                    break;
                }
            }
        }

    }

    this.remove_polygon = function () {
        drawfeatureGroup.eachLayer(function (layer) {
            if (layer instanceof L.Polygon) {
                //Do marker specific actions here
                drawfeatureGroup.removeLayer(layer);
            }
        });
    }
    
    this.addTrafficLayer = function () {
        yandexTraffic.addTo(map_object);
    }

    this.removeTrafficLayer = function () {
        map_object.removeLayer(yandexTraffic);
    }

    this.map_fit = function () {
        map_object.fitBounds(featureGroup.getBounds());
    }

    this.clear_featureGroup = function () {
        featureGroup = new L.featureGroup();
        this.featureGroup = featureGroup;
    }

    this.add_featureGroup = function (marker) {
        featureGroup.addLayer(marker);
    }

    this.add_currentlocationmarker = function (_settings) {
        if (map_object != null) {

            var draggable = false;
            var lat;
            var lng;
            var popup = "";
            var number = "";
            var className = "";
            var areaIcon = false;
            var iconUrl = "/leaflet/images/gps.gif";
            var tooltip = "";
            var unique_code = "";
            var drag = null;
            var dragstart = null;
            var dragend = null;
            var click = null;
            var unique_id = 0;
            var borderColor = "";
            var marker_type = "0";
            var opacity = 1;
            var route_index = 0;
            var isWidth = 20;
            var isHeight = 20;
            var iaWidth = 10;
            var iaHeight = 10;
            var paWidth = 0;
            var paHeight = -12;

            if (_settings.lat) lat = _settings.lat;
            if (_settings.lng) lng = _settings.lng;
            if (_settings.popup) popup = _settings.popup;
            if (_settings.draggable) draggable = _settings.draggable;
            if (_settings.number) number = _settings.number;
            if (_settings.className) className = _settings.className;
            if (_settings.borderColor) borderColor = _settings.borderColor;
            if (_settings.iconUrl) iconUrl = _settings.iconUrl;
            if (_settings.tooltip) tooltip = _settings.tooltip;
            if (_settings.unique_code) unique_code = _settings.unique_code;
            if (_settings.unique_id) unique_id = _settings.unique_id;
            if (_settings.drag) drag = _settings.drag;
            if (_settings.dragstart) dragstart = _settings.dragstart;
            if (_settings.dragend) dragend = _settings.dragend;
            if (_settings.click) click = _settings.click;
            if (_settings.marker_type) marker_type = _settings.marker_type;
            if (_settings.areaIcon) areaIcon = _settings.areaIcon;
            if (_settings.route_index) route_index = _settings.route_index;
            if (_settings.opacity) opacity = _settings.opacity;
            if (_settings.isWidth) { 
                isWidth = _settings.isWidth;
                iaWidth = isWidth / 2;
            }
            if (_settings.isHeight) {
                isHeight = _settings.isHeight;
                iaHeight = isHeight;
                paHeight = 8 - isHeight;
            }
            //if (_settings.iaWidth) iaWidth = _settings.iaWidth;
            //if (_settings.iaHeight) iaHeight = _settings.iaHeight;
            if (_settings.paWidth) paWidth = _settings.paWidth;
            //if (_settings.paHeight) paHeight = _settings.paHeight;

            var _icon = null;
            if (number != "") {
                _icon = new NumberedDivIcon({ iconUrl: iconUrl, number: number });
            }
            else if (className != "") {
                _icon = new ContentDivIcon({ className: className, borderColor: borderColor });
            }
            else if (areaIcon) {
                _icon = new AreaDivIcon({ iconUrl: iconUrl });
            }
            else {
                _icon = new L.icon({
                    iconUrl: iconUrl, iconSize: new L.Point(isWidth, isHeight),
                    iconAnchor: new L.Point(iaWidth, iaHeight),
                    popupAnchor: new L.Point(paWidth, paHeight),
                });
            }

            if (marker_current_location != null) {
                map_object.removeLayer(marker_current_location);
            }

            marker_current_location = L.marker([lat, lng], { icon: _icon, draggable: draggable, opacity: opacity });
            if (popup != "") marker_current_location.bindPopup(popup, { maxWidth: 800 });
            if (tooltip != "") marker_current_location.bindTooltip(tooltip, { permanent: true, className: "", offset: [0, 0] });

            if (drag != null) {
                marker_current_location.on('drag', function (e) {
                    //console.log('marker drag event');
                    drag(e);
                });
            }

            if (dragstart != null) {
                marker_current_location.on('dragstart', function (e) {
                    //console.log('marker dragstart event');
                    dragstart(e);
                });
            }

            if (dragend != null) {
                marker_current_location.on('dragend', function (e) {
                    //console.log('marker dragend event');
                    dragend(e);
                });
            }

            if (click != null) {
                marker_current_location.on('click', function (e) {
                    //console.log('marker dragend event');
                    click(e);
                });
            }
            //marker_array.push({ uniqueCode: unique_code, markerType: marker_type, uniqueId: unique_id, marker: marker, route_index: route_index });
            marker_current_location.addTo(map_object);
            featureGroup.addLayer(marker_current_location);
        }
    }

}

