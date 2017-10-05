<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

TemplateLoader::addFiles(
    array(
        'ce_leaflet_map'     => 'system/modules/leaflet/templates',
        'leaflet_map_js'     => 'system/modules/leaflet/templates',
        'leaflet_map_html'   => 'system/modules/leaflet/templates',
        'mod_leaflet_map'    => 'system/modules/leaflet/templates',
        'be_leaflet_geocode' => 'system/modules/leaflet/templates',
        'be_leaflet_about'   => 'system/modules/leaflet/templates',
    )
);
