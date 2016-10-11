<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */
return array(
    $GLOBALS['container']['leaflet.boot.subscriber'],
    'Netzmacht\Contao\Leaflet\Subscriber\HashSubscriber',
    $GLOBALS['container']['leaflet.subscriber.geo-json'],
);
