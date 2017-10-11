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

$GLOBALS['TL_DCA']['tl_leaflet_control_layer'] = [
    'config' => [
        'dataContainer' => 'Table',
        'sql'           => [
            'keys' => [
                'id'      => 'primary',
                'cid,lid' => 'unique',
            ],
        ],
    ],

    'fields' => [
        'id'      => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'cid'     => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'lid'     => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'mode'    => [
            'sql' => "varchar(16) NOT NULL default ''",
        ],
    ],
];
