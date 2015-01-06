L.Contao = L.Class.extend( {
    includes: L.Mixin.Events,

    maps: {},

    addMap: function (id, map) {
        this.maps[id] = map;

        this.fire('mapadded', { id: id, map: map});

        return this;
    },

    getMap: function (id) {
        if (typeof (this.maps[id]) === 'undefined') {
            return null;
        }

        return this.maps[id];
    }
});

L.Icon.Default.imagePath = 'system/modules/leaflet/assets/leaflet/leaflet/images';

window.ContaoLeaflet = new L.Contao();
