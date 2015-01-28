/**
 * Leaflet integration into contao.
 *
 * This class provides some helpers for loading layer data manages maps and map objects.
 */
L.Contao = L.Class.extend({
    includes: L.Mixin.Events,

    statics: {
        /**
         * Contao extension attribution.
         *
         * You are not allowed to remove or change it. Contact me if you want to buy an removal license.
         */
        ATTRIBUTION: ' | <a href="http://contao-leaflet.netzmacht.de/" title="Leaflet extension for Contao CMS">netzmacht <em>creative</em></a>'
    },

    /**
     * The map registry.
     */
    maps: {},

    /**
     * The icons registry.
     */
    icons: {},

    /**
     * Initialize Contao leaflet integration.
     */
    initialize: function () {
        L.Icon.Default.imagePath = 'assets/leaflet/libs/leaflet/images';

        this.setGeoJsonListeners(L.GeoJSON);
    },

    /**
     * Add map to map registry.
     *
     * @param id  The map id.
     * @param map The map object.
     *
     * @returns {L.contao}
     */
    addMap: function (id, map) {
        this.maps[id] = map;

        this.fire('map:added', {id: id, map: map});

        return this;
    },

    /**
     * Get a map from the icon map. Returns null if not set.
     *
     * @param id The mapobject
     *
     * @returns {L.Map}|{*}
     */
    getMap: function (id) {
        if (typeof (this.maps[id]) === 'undefined') {
            return null;
        }

        return this.maps[id];
    },

    /**
     * Add an icon to the icon registry.
     *
     * @param id   The icon id.
     * @param icon The icon object.
     *
     * @returns {L.contao}
     */
    addIcon: function (id, icon) {
        this.icons[id] = icon;
        this.fire('icon:added', {id: id, icon: icon});

        return this;
    },

    /**
     * Load icon definitions.
     *
     * @param icons List of icon definitions.
     *
     * @return void
     */
    loadIcons: function (icons) {
        for (var i = 0; i < icons.length; i++) {
            var icon = L[icons[i].type](icons[i].options);
            this.addIcon(icons[i].id, icon);
        }
    },

    /**
     * Get an icon by its id.
     *
     * @param id Icon id.
     *
     * @returns {L.Icon}|{L.DivIcon}|{*}
     */
    getIcon: function (id) {
        if (typeof (this.icons[id]) === 'undefined') {
            return null;
        }

        return this.icons[id];
    },

    /**
     * Layer a url into a layer using omnivore.
     *
     * @param hash        The leaflet url hash.
     * @param type        The response content format.
     * @param options     Parser options
     * @param customLayer optional custom layer.
     * @param map         Pass a map object so that the data loading events are passed to the map.
     */
    load: function (hash, type, options, customLayer, map) {
        var url   = this.createRequestUrl(hash, map),
            layer = omnivore[type](url, options, customLayer);

        if (map) {
            // Required because Control.Loading tries to get _leafet_id which is created here.
            L.stamp(layer);

            // Add listender for map bounds changes.
            if (map.options.dynamicLoad && layer.options.boundsMode == 'fit') {
                layer.options.requestHash = hash;
                map.on('moveend', layer.refreshData, layer);

                map.on('layerremove', function(e) {
                    if (e.layer === layer) {
                        map.off('moveend', layer.updateBounds, layer);
                    }
                });
            }

            map.fire('dataloading', {layer: layer});

            layer.on('ready', function () {
                map.calculateFeatureBounds(layer);
                map.fire('dataload', {layer: layer});
            });

            layer.on('error', function () {
                map.fire('dataload', {layer: layer});
            });
        }

        return layer;
    },

    /**
     * Point to layer callback. Adds a geo json point to the layer.
     *
     * @param feature The geo json feature.
     * @param latlng  The converted latlng.
     *
     * @returns {L.Marker}|{*}
     */
    pointToLayer: function (feature, latlng) {
        var type   = 'marker';
        var marker = null;

        if (feature.properties) {
            feature.properties.bounds = true;

            if (feature.properties.type) {
                type = feature.properties.type;
            }

            // constructor arguments given, use them.
            if (feature.properties.arguments) {
                marker = L[type].apply(L[type], feature.properties.arguments);

                L.Util.setOptions(marker, feature.properties.options);
            }
        }

        if (marker === null) {
            marker = L[type](latlng, feature.properties.options);
        }

        if (feature.properties) {
            if (feature.properties.radius) {
                marker.setRadius(feature.properties.radius);
            }

            if (feature.properties.icon) {
                var icon = this.getIcon(feature.properties.icon);

                if (icon) {
                    marker.setIcon(icon);
                }
            }

            this.bindPopupFromFeature(marker, feature);
        }

        this.fire('point:added', {marker: marker, feature: feature, latlng: latlng, type: type});

        return marker;
    },

    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            L.Util.setOptions(layer, feature.properties.options);

            this.bindPopupFromFeature(layer, feature);

            this.fire('feature:added', {feature: feature, layer: layer});
        }
    },

    /**
     * Bind popup from feature definitions.
     *
     * It accepts popup or popupContent as property.
     *
     * @param obj     The object
     * @param feature The geo json feature.
     */
    bindPopupFromFeature: function (obj, feature) {
        if (feature.properties) {
            if (feature.properties.popup) {
                obj.bindPopup(feature.properties.popup, feature.properties.popupOptions);
            } else if (feature.properties.popupContent) {
                obj.bindPopup(feature.properties.popupContent, feature.properties.popupOptions);
            }
        }
    },

    /**
     * Set the default geojson listeners to the prototype.
     *
     * @param obj
     */
    setGeoJsonListeners: function (obj) {
        if (obj && obj.prototype) {
            obj.prototype.options = {
                pointToLayer: this.pointToLayer.bind(this),
                onEachFeature: this.onEachFeature.bind(this)
            };
        }
    },

    /**
     * Create request url by appending the hash to the current url.
     *
     * @param {string} value The hash.
     * @param {L.Map}  map   The map.
     *
     * @returns {string}
     */
    createRequestUrl: function (value, map) {
        var bounds,
            key    = 'leaflet',
            params = document.location.search.substr(1).split('&');

        value = encodeURIComponent(value);

        if (params == '') {
            value = document.location.pathname + '?' + [key, value].join('=');
        } else {
            var i = params.length;
            var x;
            while (i--) {
                x = params[i].split('=');

                if (x[0] == key) {
                    x[1] = value;
                    params[i] = x.join('=');
                    break;
                }
            }

            if (i < 0) {
                params[params.length] = [key, value].join('=');
            }

            value = document.location.pathname + '?' + params.join('&');
        }

        if (map) {
            if (map.options.dynamicLoad) {
                bounds = map.getBounds();
                value += '&f=bbox&v=';
                value += bounds.getSouth() + ',' + bounds.getWest();
                value += ',' + bounds.getNorth() + ',' + bounds.getEast();
            }
        }

        return value;
    }
});

/**
 * Start Contao integration.
 */
L.contao = new L.Contao();
