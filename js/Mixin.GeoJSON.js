/**
 * Add update bounds method for geo json layers. It is triggered when map bounds changed and make a new request
 * to get the data in the new bounds.
 */
L.GeoJSON.include({
    /**
     * Update bounds.
     *
     * @param {L.Event} e The subscribed event.
     */
    refreshData: function(e) {
        var dataLayer = L.geoJson(),
            layer     = this;

        dataLayer.on('ready', function() {
            var i, layers = layer.getLayers();

            // Clear old data.
            for (i = 0; i < layers.length; i++) {
                layer.removeLayer(layers[i]);
            }

            // Copy data from temporary layer.
            layers = this.getLayers();
            for (i = 0; i < layers.length; i++) {
                this.removeLayer(layers[i]);
                layer.addLayer(layers[i]);
            }
        });

        // TODO: Allow other data formats.
        omnivore.geojson(L.contao.createRequestUrl(this.options.requestHash, e.target), null, dataLayer);
    }
});
