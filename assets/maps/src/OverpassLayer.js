/**
 * Get the bounds as overpass bbox string.
 *
 * @returns {string}
 */
L.LatLngBounds.prototype.toOverpassBBoxString = function () {
    var a = this._southWest,
        b = this._northEast;

    return [a.lat, a.lng, b.lat, b.lng].join(",");
};

/**
 * Implementation of the overpass layer. Heavily inspired by
 * https://github.com/kartenkarsten/leaflet-layer-overpass.
 */
L.OverPassLayer = L.FeatureGroup.extend({
    options: {
        minZoom: 0,
        endpoint: '//overpass-api.de/api/',
        query: '(node(BBOX)[organic];node(BBOX)[second_hand];);out qt;',
        amenityIcons: {}
    },
    /**
     * Initialize the layer.
     *
     * @param options
     */
    initialize: function (options) {
        L.Util.setOptions(this, options);

        this.options.pointToLayer = this.pointToLayer;
        this.options.onEachFeature = this.onEachFeature;
        this.options.dynamicLoad = this.options.query.match(/BBOX/g) ? true : false;

        this._layer  = L.geoJson();
        this._layers = {};

        this.addLayer(this._layer);
    },
    /**
     * Refresh the data of the layer.
     *
     * TODO: Implement some caching.
     */
    refreshData: function () {
        if (this._map.getZoom() < this.options.minZoom) {
            return;
        }

        var bounds = this._map.getBounds().toOverpassBBoxString();
        var query  = this.options.query.replace(/(BBOX)/g, bounds);
        var url    = this.options.endpoint + "interpreter?data=[out:json];" + query;

        this._map.fire('dataloading', {layer: this});

        this.request(url, function (error, response) {
            var data     = JSON.parse(response.response);
            var features = osmtogeojson(data);
            var layer    = L.geoJson(features, {
                pointToLayer: this.options.pointToLayer.bind(this),
                onEachFeature: this.options.onEachFeature.bind(this)
            });

            this.addLayer(layer);
            this.removeLayer(this._layer);
            this._layer = layer;

            if (this.options.boundsMode === 'extend' && layer.getBounds().isValid()) {
                var bounds = this._map.getBounds();
                bounds     = bounds.extend(layer.getBounds());

                this._map.fitBounds(bounds, this._map.getBoundsOptions());
            }

            this._map.fire('dataload', {layer: this});
        }.bind(this));
    },
    /**
     * @param map
     */
    onAdd: function (map) {
        if (this.options.boundsMode === 'fit' && this.options.dynamicLoad) {
            map.on('moveend', this.refreshData, this);
        }

        this.refreshData();
    },
    pointToLayer: function (feature, latlng) {
        var type   = 'marker';
        var icon   = null;
        var marker = L.marker(latlng, feature.properties.options);

        if (feature.properties) {
            if (feature.properties.radius) {
                marker.setRadius(feature.properties.radius);
            }

            if (feature.properties.icon) {
                icon = this._map.getIcon(feature.properties.icon);

            } else if (feature.properties.tags
                && feature.properties.tags.amenity
                && this.options.amenityIcons[feature.properties.tags.amenity]
            ) {
                console.log(this.options.amenityIcons[feature.properties.tags.amenity]);
                icon = L.contao.getIcon(this.options.amenityIcons[feature.properties.tags.amenity]);
            }

            if (icon) {
                marker.setIcon(icon);
            }
        }

        if (this.options.overpassPopup) {
            marker.bindPopup(this.options.overpassPopup(feature, marker));
        }

        this._map.fire('point:added', {marker: marker, feature: feature, latlng: latlng, type: type});

        return marker;
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            L.Util.setOptions(layer, feature.properties.options);

            if (this.options.overpassPopup) {
                layer.bindPopup(this.options.overpassPopup(feature, layer));
            }

            this._map.fire('feature:added', {feature: feature, layer: layer});
        }
    },
    /**
     * Make an ajax request. Clone of corslite from MapQuest.
     */
    request: function (url, callback, cors) {
        var sent = false;

        if (typeof window.XMLHttpRequest === 'undefined') {
            return callback(Error('Browser not supported'));
        }

        if (typeof cors === 'undefined') {
            var m = url.match(/^\s*https?:\/\/[^\/]*/);
            cors = m && (m[0] !== location.protocol + '//' + location.hostname +
                (location.port ? ':' + location.port : ''));
        }

        var x = new window.XMLHttpRequest();

        function isSuccessful(status) {
            return status >= 200 && status < 300 || status === 304;
        }

        if (cors && !('withCredentials' in x)) {
            // IE8-9
            x = new window.XDomainRequest();

            // Ensure callback is never called synchronously, i.e., before
            // x.send() returns (this has been observed in the wild).
            // See https://github.com/mapbox/mapbox.js/issues/472
            var original = callback;
            callback = function() {
                if (sent) {
                    original.apply(this, arguments);
                } else {
                    var that = this, args = arguments;
                    setTimeout(function() {
                        original.apply(that, args);
                    }, 0);
                }
            }
        }

        function loaded() {
            if (
                // XDomainRequest
            x.status === undefined ||
            // modern browsers
            isSuccessful(x.status)) callback.call(x, null, x);
            else callback.call(x, x, null);
        }

        // Both `onreadystatechange` and `onload` can fire. `onreadystatechange`
        // has [been supported for longer](http://stackoverflow.com/a/9181508/229001).
        if ('onload' in x) {
            x.onload = loaded;
        } else {
            x.onreadystatechange = function readystate() {
                if (x.readyState === 4) {
                    loaded();
                }
            };
        }

        // Call the callback with the XMLHttpRequest object as an error and prevent
        // it from ever being called again by reassigning it to `noop`
        x.onerror = function error(evt) {
            // XDomainRequest provides no evt parameter
            callback.call(this, evt || true, null);
            callback = function() { };
        };

        // IE9 must have onprogress be set to a unique function.
        x.onprogress = function() { };

        x.ontimeout = function(evt) {
            callback.call(this, evt, null);
            callback = function() { };
        };

        x.onabort = function(evt) {
            callback.call(this, evt, null);
            callback = function() { };
        };

        // GET is the only supported HTTP Verb by XDomainRequest and is the
        // only one supported here.
        x.open('GET', url, true);

        // Send the request. Sending data is not supported.
        x.send(null);
        sent = true;

        return x;
    }
});
