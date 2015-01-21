
/**
 * Extend map so that it can calculate their bounds depending of the features with the property affectBounds.
 */
L.Map.include({
    _dynamicBounds: null,

    /**
     * Calculate feature bounds.
     *
     * Use this method without any arguments to scan the whole map for features.
     *
     * If a layer is passed then only this layer is used if the map option adjustBounds is set or the force flag
     * is given.
     *
     * @param {L.Layer} layer Optional limit to a layer.
     * @param {bool}    force Force scaning of a layer no matter if adjustBounds is set.
     *
     * @return void
     */
    calculateFeatureBounds: function(layer, force) {
        if (layer) {
            if (!this.options.adjustBounds && !force) {
                return;
            }

            this._scanForBounds(layer);
        } else {
            this.eachLayer(this._scanForBounds, this);
        }

        if (this._dynamicBounds) {
            this.fitBounds(this._dynamicBounds);
        }
    },

    /**
     * Scan recursively for bounds in a layer and extend _dynamicBounds if any found.
     *
     * @param {L.Layer} layer The layer
     * @private
     */
    _scanForBounds: function(layer) {
        var source;

        if (layer.feature && (!layer.feature.properties || !layer.feature.properties.ignoreForBounds)) {
            if (layer.getBounds) {
                source = layer.getBounds();

                if (source.isValid()) {
                    if (this._dynamicBounds) {
                        this._dynamicBounds.extend(source);
                    } else {
                        this._dynamicBounds = L.latLngBounds(source.getSouthWest(), source.getNorthEast());
                    }
                }
            } else if (layer.getLatLng) {
                source = layer.getLatLng();

                if (this._dynamicBounds) {
                    this._dynamicBounds.extend(source);
                } else {
                    this._dynamicBounds = L.latLngBounds(source, source);
                }
            }
        } else if (layer instanceof L.MarkerClusterGroup && layer.options.affectBounds) {
            source = layer.getBounds();

            if (source.isValid()) {
                if (this._dynamicBounds) {
                    this._dynamicBounds.extend(source);
                } else {
                    this._dynamicBounds = L.latLngBounds(source.getSouthWest(), source.getNorthEast());
                }
            }
        } else if ((!layer.options || (layer.options && layer.options.affectBounds)) && layer.eachLayer) {
            layer.eachLayer(this._scanForBounds, this);
        }
    }
});
