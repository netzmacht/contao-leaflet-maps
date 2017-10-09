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

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['leaflet'] = [
    'type'      => ['name', 'type', 'headline'],
    'leaflet'   => ['leaflet_map', 'leaflet_mapId', 'leaflet_width', 'leaflet_height', 'leaflet_template'],
    'templates' => [':hide', 'customTpl'],
    'protected' => [':hide', 'protected'],
    'expert'    => [':hide', 'guests', 'cssID', 'space'],
    'invisible' => [':hide', 'invisible', 'start', 'start'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_map'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['leaflet_map'],
    'inputType'        => 'select',
    'exclude'          => true,
    'options_callback' => ['netzmacht.contao_leaflet_maps.listeners.dca.frontend_integration', 'getMaps'],
    'wizard'           => [
        ['netzmacht.contao_leaflet_maps.listeners.dca.frontend_integration', 'getEditMapLink'],
    ],
    'eval'             => [
        'tl_class' => 'w50 wizard',
        'chosen'   => true,
    ],
    'sql'              => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_mapId'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['leaflet_mapId'],
    'inputType' => 'text',
    'exclude'   => true,
    'eval'      => [
        'tl_class'  => 'w50',
        'chosen'    => true,
        'maxlength' => 16,
    ],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_width'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['leaflet_width'],
    'inputType' => 'inputUnit',
    'options'   => ['px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'],
    'search'    => false,
    'exclude'   => true,
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'clr w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
];


$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_height'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['leaflet_height'],
    'inputType' => 'inputUnit',
    'options'   => ['px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'],
    'search'    => false,
    'exclude'   => true,
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_template'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['leaflet_template'],
    'inputType'        => 'select',
    'exclude'          => true,
    'options_callback' => ['netzmacht.contao_toolkit.dca.listeners.template_options', 'handleOptionsCallback'],
    'eval'             => [
        'tl_class' => 'w50',
        'chosen'   => true,
    ],
    'toolkit'          => [
        'template_options' => [
            'prefix' => 'leaflet_map_js',
        ],
    ],
    'sql'              => "varchar(64) NOT NULL default ''",
];
