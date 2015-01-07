L.Contao = L.Class.extend( {
    includes: L.Mixin.Events,

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
    initialize: function() {
        L.Icon.Default.imagePath = 'assets/leaflet/libs/leaflet/images';
    },

    /**
     * Add map to map registry.
     *
     * @param id  The map id.
     * @param map The map object.
     *
     * @returns {L.Contao}
     */
    addMap: function (id, map) {
        this.maps[id] = map;

        this.fire('map:added', { id: id, map: map});

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
     * @returns {L.Contao}
     */
    addIcon: function(id, icon) {
        this.icons[id] = icon;
        this.fire('icon:added', { id: id, icon: icon});

        return this;
    },

    /**
     * Load icon definitions.
     *
     * @param icons List of icon definitions.
     *
     * @return void
     */
    loadIcons: function(icons) {
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
    getIcon: function(id) {
        if (typeof (this.icons[id]) === 'undefined') {
            return null;
        }

        return this.icons[id];
    },

    /**
     * Point to layer callback. Adds a geo json point to the layer.
     *
     * @param feature The geo json feature.
     * @param latlng  The converted latlng.
     *
     * @returns {L.Marker}|{*}
     */
    pointToLayer: function(feature, latlng) {
        var marker = L.marker(latlng, feature.properties.options);

        if (feature.properties && feature.properties.icon) {
            var icon = this.getIcon(feature.properties.icon);

            if (icon) {
                marker.setIcon(icon);
            }
        }

        this.applyFeatureMethods(marker, feature);
        this.fire('marker:created', { marker: marker, feature: feature, latlng: latlng });

        return marker;
    },

    /**
     * Apply feature methods.
     *
     * @param obj
     * @param feature
     */
    applyFeatureMethods: function(obj, feature) {
        if (feature.properties && feature.properties.methods) {
            for (var i=0; i < feature.properties.methods.length; i++) {
                var method = feature.properties.methods[i];

                obj[method[0]].apply(obj, method[1]);
            }
        }
    }
});

window.ContaoLeaflet = new L.Contao();
