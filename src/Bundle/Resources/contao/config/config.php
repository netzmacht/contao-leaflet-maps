<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

/*
 * Backend modules
 */

array_insert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'leaflet' => [
            'leaflet_map'   => [
                'tables'     => [
                    'tl_leaflet_map',
                    'tl_leaflet_control',
                ],
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/map.png',
                'stylesheet' => 'bundles/netzmachtcontaoleaflet/css/backend.css',
            ],
            'leaflet_layer' => [
                'tables'     => [
                    'tl_leaflet_layer',
                    'tl_leaflet_marker',
                    'tl_leaflet_vector',
                    'tl_leaflet_icon',
                    'tl_leaflet_style',
                    'tl_leaflet_popup',
                ],
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/layers.png',
                'stylesheet' => 'bundles/netzmachtcontaoleaflet/css/backend.css',
                'javascript' => 'bundles/netzmachtcontaoleaflet/js/backend.js',
            ],
        ],
    ]
);

if (TL_MODE === 'BE') {
    $GLOBALS['TL_CSS'][] = 'bundles/netzmachtcontaoleaflet/css/backend_global.css';
}


/*
 * Models
 */

$GLOBALS['TL_MODELS']['tl_leaflet_control'] = \Netzmacht\Contao\Leaflet\Model\ControlModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_icon']    = \Netzmacht\Contao\Leaflet\Model\IconModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_layer']   = \Netzmacht\Contao\Leaflet\Model\LayerModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_map']     = \Netzmacht\Contao\Leaflet\Model\MapModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_marker']  = \Netzmacht\Contao\Leaflet\Model\MarkerModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_popup']   = \Netzmacht\Contao\Leaflet\Model\PopupModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_style']   = \Netzmacht\Contao\Leaflet\Model\StyleModel::class;
$GLOBALS['TL_MODELS']['tl_leaflet_vector']  = \Netzmacht\Contao\Leaflet\Model\VectorModel::class;


/*
 * Permissions
 */

$GLOBALS['TL_PERMISSIONS'][] = 'leaflet_layers';
$GLOBALS['TL_PERMISSIONS'][] = 'leaflet_layer_permissions';
$GLOBALS['TL_PERMISSIONS'][] = 'leaflet_tables';
