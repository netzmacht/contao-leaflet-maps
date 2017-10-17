/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Provide methods to handle Ajax requests.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
var ContaoLeafletAjaxRequest = {
    /**
     * Toggle the visibility of an element
     *
     * @param {object} el    The DOM element
     * @param {string} id    The ID of the target element
     * @param {string} table The table name
     *
     * @returns {boolean}
     */
    toggleVisibility: function (el, id, table) {
        el.blur();

        var img = null,
            image = $(el).getFirst('img'),
            published = (image.get('data-state') == 1),
            div = el.getParent('div'),
            index, next, icon, icond, pa;
        img = div.getParent('li').getFirst('.tl_left img.list-icon');

        // Change the icon
        if (img !== null && img.nodeName.toLowerCase() == 'img') {
            icon = img.get('data-icon');
            icond = img.get('data-icon-disabled');

            img.src = !published ? icon : icond;
        }

        // Send request
        if (!published) {
            image.src = AjaxRequest.themePath + 'icons/visible.svg';
            image.set('data-state', 1);
            new Request.Contao({'url': window.location.href, 'followRedirects': false}).get({
                'tid': id,
                'state': 1,
                'rt': Contao.request_token
            });
        } else {
            image.src = AjaxRequest.themePath + 'icons/invisible.svg';
            image.set('data-state', 0);
            new Request.Contao({'url': window.location.href, 'followRedirects': false}).get({
                'tid': id,
                'state': 0,
                'rt': Contao.request_token
            });
        }

        return false;
    }
};
