<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */
return array(
    $GLOBALS['container']['leaflet.boot.subscriber'],
    'Netzmacht\Contao\Leaflet\Subscriber\HashSubscriber',
    'Netzmacht\Contao\Leaflet\Subscriber\GeoJsonSubscriber',
);
