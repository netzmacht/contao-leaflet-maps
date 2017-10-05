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
