<?php

/*
 * Backend module.
 */
$GLOBALS['BE_MOD']['content']['leaflet'] = array(
    'tables' => array(
        'tl_leaflet_map',
        'tl_leaflet_layer',
        'tl_leaflet_control',
        'tl_leaflet_marker',
        'tl_leaflet_vector',
        'tl_leaflet_icon',
    ),
    'icon'       => 'system/modules/leaflet/assets/img/leaflet.png',
    'stylesheet' => 'system/modules/leaflet/assets/css/backend.css',
);

/*
 * Content elements.
 */
$GLOBALS['TL_CTE']['includes']['leaflet'] = 'Netzmacht\Contao\Leaflet\LeafletMapElement';


/*
 * Models.
 */
$GLOBALS['TL_MODELS']['tl_leaflet_control'] = 'Netzmacht\Contao\Leaflet\Model\ControlModel';
$GLOBALS['TL_MODELS']['tl_leaflet_icon']    = 'Netzmacht\Contao\Leaflet\Model\IconModel';
$GLOBALS['TL_MODELS']['tl_leaflet_layer']   = 'Netzmacht\Contao\Leaflet\Model\LayerModel';
$GLOBALS['TL_MODELS']['tl_leaflet_map']     = 'Netzmacht\Contao\Leaflet\Model\MapModel';
$GLOBALS['TL_MODELS']['tl_leaflet_marker']  = 'Netzmacht\Contao\Leaflet\Model\MarkerModel';


/*
 * Leaflet mappers.
 *
 * Mappers do the translations between the database models and the leaflet definition.
 */
$GLOBALS['LEAFLET_MAPPERS']   = array();
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\MapMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\ProviderLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\MarkersLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\ZoomControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\ScaleControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\LayersControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\AttributionControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\UI\MarkerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Type\ImageIconMapper';

/*
 * Leaflet encoders.
 *
 * The encoders transforms the definitions into javascript. The encoders has to be an implementation of the
 * EventDispatcherInterface of the event dispatcher.
 *
 * You can define the encoders using the syntax of the cca event dispatcher implementation.
 *
 * @see https://github.com/contao-community-alliance/event-dispatcher#event-subscriber-per-configuration
 */
$GLOBALS['LEAFLET_ENCODERS']   = array();
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\Javascript\Subscriber\EncoderSubscriber';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\MapEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\ControlEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\GroupEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\RasterEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\VectorEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\UIEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\LeafletPHP\Encoder\TypeEncoder';
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\Contao\Leaflet\Subscriber\EncoderSubscriber';

/*
 * Leaflet layer types.
 *
 * The type is used for the database driven definitions.
 */
$GLOBALS['LEAFLET_LAYERS'] = array
(
    'default'  => array
    (
        'children' => true,
        'icon'     => '',
    ),
    'provider' => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/tile.png',
    ),
    'group'    => array
    (
        'children' => true,
        'icon'     => 'system/modules/leaflet/assets/img/group.png',
    ),
    'elements' => array
    (
        'children' => true,
        'filter'   => 'whitelist',
        'layers'   => array('markers', 'vectors'),
        'icon'     => 'system/modules/leaflet/assets/img/map.png',
    ),
    'markers'  => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/markers.png',
        'markers'  => true,
    ),
    'vectors'  => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/vectors.png',
        'vectors'  => true,
    ),
);

/*
 * leaflet controls.
 *
 * Supported leaflet control types. Register your type for the database driven definition here.
 */
$GLOBALS['LEAFLET_CONTROLS']   = array();
$GLOBALS['LEAFLET_CONTROLS'][] = 'zoom';
$GLOBALS['LEAFLET_CONTROLS'][] = 'layers';
$GLOBALS['LEAFLET_CONTROLS'][] = 'scale';
$GLOBALS['LEAFLET_CONTROLS'][] = 'attribution';

$GLOBALS['LEAFLET_ICONS'] = array('image', 'div');

/*
 * Leaflet tile layer providers.
 */
require_once TL_ROOT . '/system/modules/leaflet/config/leaflet_providers.php';


/*
 * Leaflet assets.
 *
 * The leaflet definition are aware of the required javascript libraries. Register the assets so that they are
 * loaded automatically.
 *
 * Each entry is an array of 2 values. The first is the resource. The second is a type. Supported types are:
 *  - url:    An valid url.
 *  - file:   An file path relative to the Contao Root.
 *  - source: Inline css/javascript.
 */
$GLOBALS['LEAFLET_ASSETS']['leaflet'] = array(
    'css' => array(
        array('assets/leaflet/libs/leaflet/leaflet.css', 'file')
    ),
    'javascript' => array(
        array('assets/leaflet/libs/leaflet/leaflet.js', 'file')
    )
);

$GLOBALS['LEAFLET_ASSETS']['leaflet-providers'] = array(
    'javascript' => array(
        array('assets/leaflet/libs/leaflet-providers/leaflet-providers.js', 'file')
    )
);

$GLOBALS['LEAFLET_ASSETS']['leaflet-ajax'] = array(
    'javascript' => array(
        array('assets/leaflet/libs/leaflet-ajax/leaflet.ajax.min.js', 'file')
    )
);

