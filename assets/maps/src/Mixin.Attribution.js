/**
 * Attribution handling.
 */
L.Control.Attribution.addInitHook(function() {
    this.options.prefix += L.Contao.ATTRIBUTION;
});

L.Control.Attribution.include({
    setPrefix: function (prefix) {
        if (prefix.indexOf(L.Contao.ATTRIBUTION) === -1) {
            prefix += L.Contao.ATTRIBUTION;
        }

        this.options.prefix = prefix;

        this._update();
        return this;
    }
});
