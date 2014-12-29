L.Contao = L.Class.extend( {
    includes: L.Mixin.Events,

    maps: {},

    addMap: function (id, map) {
        this.maps[id] = map;

        this.fire('mapadded', { id: id, map: map});

        return this;
    },

    getMap: function (id) {
        if (typeof (this.map[id]) === 'undefined') {
            return null;
        }

        return this.map[id]
    }
});

window.ContaoLeaflet = new L.Contao();
