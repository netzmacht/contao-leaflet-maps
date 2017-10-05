<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

return array(
    $GLOBALS['container']['leaflet.boot.subscriber'],
    'Netzmacht\Contao\Leaflet\Subscriber\HashSubscriber',
    $GLOBALS['container']['leaflet.subscriber.geo-json'],
);
