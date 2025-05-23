
var NumberedDivIcon = L.Icon.extend({
    options: {
        // EDIT THIS TO POINT TO THE FILE AT http://www.charliecroom.com/marker_hole.png (or your own marker)
        iconUrl: '/leaflet/images/marker-icon.png',
        number: '',
        shadowUrl: '/leaflet/images/marker-shadow.png',
        iconSize: new L.Point(25, 41),
        iconAnchor: new L.Point(13, 41),
        popupAnchor: new L.Point(0, -33),
        /*
		iconAnchor: (Point)
		popupAnchor: (Point)
		*/
        className: 'leaflet-div-icon'
    },

    createIcon: function () {
        var div = document.createElement('div');
        var img = this._createImg(this.options['iconUrl']);
        var numdiv = document.createElement('div');
        numdiv.setAttribute("class", 'number');
        numdiv.innerHTML = this.options['number'] || '';
        div.appendChild(img);
        div.appendChild(numdiv);
        this._setIconStyles(div, 'icon');
        return div;
    },

    //you could change this to add a shadow like in the normal marker if you really wanted
    createShadow: function () {
        var div = document.createElement('div');
        var img = this._createImg(this.options['shadowUrl']);
        var numdiv = document.createElement('div');
        //numdiv.setAttribute("class", "number");
        //numdiv.innerHTML = this.options['number'] || '';
        div.appendChild(img);
        div.appendChild(numdiv);
        this._setIconStyles(div, 'icon');
        return div;
    }
});


var ContentDivIcon = L.Icon.extend({
    options: {
        // EDIT THIS TO POINT TO THE FILE AT http://www.charliecroom.com/marker_hole.png (or your own marker)
        iconUrl: '/leaflet/images/marker-icon.png',
        number: '',
        shadowUrl: '/leaflet/images/marker-shadow.png',
        iconSize: new L.Point(22, 22),
        iconAnchor: new L.Point(11, 16),
        //popupAnchor: new L.Point(0, 11),
        /*
		iconAnchor: (Point)
		popupAnchor: (Point)
		*/
        className: 'leaflet-div-icon'
    },

    createIcon: function () {
        var div = document.createElement('div');
        var numdiv = document.createElement('div');
        numdiv.setAttribute("class", this.options['className']);
        numdiv.setAttribute("style", "border-color:" + this.options['borderColor'] || '#000000');
        numdiv.innerHTML = '';
        div.appendChild(numdiv);
        this._setIconStyles(div, 'icon');
        return div;
    },

    //you could change this to add a shadow like in the normal marker if you really wanted
    createShadow: function () {
        return null;
    }
});


var AreaDivIcon = L.Icon.extend({
    options: {
        // EDIT THIS TO POINT TO THE FILE AT http://www.charliecroom.com/marker_hole.png (or your own marker)
        iconUrl: '/leaflet/images/marker-icon.png',
        number: '',
        shadowUrl: '/leaflet/images/marker-shadow.png',
        iconSize: new L.Point(40, 33),
        iconAnchor: new L.Point(10, 33),
        popupAnchor: new L.Point(0, -27),
        /*
		iconAnchor: (Point)
		popupAnchor: (Point)
		*/
        className: 'leaflet-div-icon'
    },

    createIcon: function () {
        var div = document.createElement('div');
        var img = this._createImg(this.options['iconUrl']);
        div.appendChild(img);
        this._setIconStyles(div, 'icon');
        return div;
    },

    //you could change this to add a shadow like in the normal marker if you really wanted
    createShadow: function () {
        return null;
    }
});


var ClusterMarkerDivIcon = L.Icon.extend({
    options: {
        // EDIT THIS TO POINT TO THE FILE AT http://www.charliecroom.com/marker_hole.png (or your own marker)
        iconUrl: '/leaflet/images/marker-icon.png',
        number: '',
        shadowUrl: '/leaflet/images/marker-shadow.png',
        iconSize: new L.Point(36, 36),
        iconAnchor: new L.Point(18, 36),
        popupAnchor: new L.Point(0, -31),
        /*
		iconAnchor: (Point)
		popupAnchor: (Point)
		*/
        className: 'leaflet-div-icon'
    },

    createIcon: function () {
        var div = document.createElement('div');
        var numdiv = document.createElement('div');
        var numSpan = document.createElement('span');
        numdiv.setAttribute("class", this.options['className']);
        numdiv.setAttribute("style", "background-color:" + this.options['backgroundColor'] || '#000000');
        numSpan.innerHTML = this.options['number'] || '';
        numdiv.appendChild(numSpan);
        div.appendChild(numdiv);
        this._setIconStyles(div, 'icon');
        return div;
    },

    //you could change this to add a shadow like in the normal marker if you really wanted
    createShadow: function () {
        return null;
    }
});