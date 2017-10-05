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

$GLOBALS['TL_DCA']['tl_leaflet_map_layer'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'sql'              => array
        (
            'keys' => array
            (
                'id'      => 'primary',
                'mid,lid' => 'unique',
            )
        )
    ),

    'fields' => array
    (
        'id'           => array
        (
            'sql' => 'int(10) unsigned NOT NULL auto_increment'
        ),
        'tstamp'       => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting'      => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'mid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'lid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
    )
);
