L.Contao = L.Class.extend( {
    includes: L.Mixin.Events,

    maps: {},

    icons: {},

    addMap: function (id, map) {
        this.maps[id] = map;

        this.fire('map:added', { id: id, map: map});

        return this;
    },

    getMap: function (id) {
        if (typeof (this.maps[id]) === 'undefined') {
            return null;
        }

        return this.maps[id];
    },

    addIcon: function(id, icon) {
        this.icons[id] = icon;
        this.fire('icon:added', { id: id, icon: icon});

        return this;
    },

    getIcon: function(id) {
        if (typeof (this.icons[id]) === 'undefined') {
            return null;
        }

        return this.icons[id];
    },

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

    applyFeatureMethods: function(obj, feature) {
        if (feature.properties && feature.properties.methods) {
            for (var i=0; i < feature.properties.methods.length; i++) {
                var method = feature.properties.methods[i];

                obj[method[0]].apply(obj, method[1]);
            }
        }
    }
});

L.Icon.Default.imagePath = 'assets/leaflet/libs/leaflet/images';

window.ContaoLeaflet = new L.Contao();
