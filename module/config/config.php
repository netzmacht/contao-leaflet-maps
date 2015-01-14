<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

/*
 * Backend module.
 */
array_insert(
    $GLOBALS['BE_MOD'],
    1,
    array(
        'leaflet' => array
        (
            'leaflet_map' => array
            (
                'tables' => array
                (
                    'tl_leaflet_map',
                    'tl_leaflet_control',
                ),
                'icon'       => 'system/modules/leaflet/assets/img/map.png',
                'stylesheet' => 'system/modules/leaflet/assets/css/backend.css',
            ),
            'leaflet_layer' => array
            (
                'tables' => array
                (
                    'tl_leaflet_layer',
                    'tl_leaflet_marker',
                    'tl_leaflet_vector',
                    'tl_leaflet_icon',
                    'tl_leaflet_style',
                ),
                'icon'       => 'system/modules/leaflet/assets/img/layers.png',
                'stylesheet' => 'system/modules/leaflet/assets/css/backend.css',
            ),
            'leaflet_credits' => array
            (
                'callback'   => 'Netzmacht\Contao\Leaflet\Backend\Credits',
                'icon'       => 'system/modules/leaflet/assets/img/info.png',
                'stylesheet' => 'system/modules/leaflet/assets/css/credits.css',
            )
        )
    )
);

/*
 * Content elements.
 */
$GLOBALS['TL_CTE']['includes']['leaflet'] = 'Netzmacht\Contao\Leaflet\Frontend\MapElement';

/*
 * Frontend modules
 */
$GLOBALS['FE_MOD']['includes']['leaflet'] = 'Netzmacht\Contao\Leaflet\Frontend\MapModule';

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Netzmacht\Contao\Leaflet\Frontend\Hooks', 'replaceInsertTags');

/*
 * Models.
 */
$GLOBALS['TL_MODELS']['tl_leaflet_control'] = 'Netzmacht\Contao\Leaflet\Model\ControlModel';
$GLOBALS['TL_MODELS']['tl_leaflet_icon']    = 'Netzmacht\Contao\Leaflet\Model\IconModel';
$GLOBALS['TL_MODELS']['tl_leaflet_layer']   = 'Netzmacht\Contao\Leaflet\Model\LayerModel';
$GLOBALS['TL_MODELS']['tl_leaflet_map']     = 'Netzmacht\Contao\Leaflet\Model\MapModel';
$GLOBALS['TL_MODELS']['tl_leaflet_marker']  = 'Netzmacht\Contao\Leaflet\Model\MarkerModel';
$GLOBALS['TL_MODELS']['tl_leaflet_style']   = 'Netzmacht\Contao\Leaflet\Model\StyleModel';
$GLOBALS['TL_MODELS']['tl_leaflet_vector']  = 'Netzmacht\Contao\Leaflet\Model\VectorModel';


/*
 * Leaflet mappers.
 *
 * Mappers do the translations between the database models and the leaflet definition.
 */
$GLOBALS['LEAFLET_MAPPERS']   = array();
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\MapMapper';

// Layer mappers.
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\ProviderLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\MarkersLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\GroupLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\VectorsLayerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Layer\ReferenceLayerMapper';

// Control mappers.
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\ZoomControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\ScaleControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\LayersControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\AttributionControlMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Control\LoadingControlMapper';

// Vector mappers.
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\PolylineMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\MultiPolylineMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\PolygonMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\MultiPolygonMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\CircleMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\CircleMarkerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Vector\RectangleMapper';

// Miscellaneous mappers.
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\UI\MarkerMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Type\ImageIconMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Type\DivIconMapper';
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\Style\FixedStyleMapper';

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
    'provider' => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/tile.png',
        'label'    => function ($row, $label) {
            if (!empty($GLOBALS['TL_LANG']['leaflet_provider'][$row['tile_provider']][0])) {
                $provider = $GLOBALS['TL_LANG']['leaflet_provider'][$row['tile_provider']][0];
            } else {
                $provider = $row['tile_provider'];
            }

            $label .= sprintf('<span class="tl_gray"> (%s)</span>', $provider);

            return $label;
        }
    ),
    'group'    => array
    (
        'children' => true,
        'icon'     => 'system/modules/leaflet/assets/img/group.png',
    ),
    'markers'  => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/markers.png',
        'markers'  => true,
        'label'    => function ($row, $label) {
            $count  = \Netzmacht\Contao\Leaflet\Model\MarkerModel::countBy('pid', $row['id']);
            $label .= sprintf(
                '<span class="tl_gray"> (%s %s)</span>',
                $count,
                $GLOBALS['TL_LANG']['tl_leaflet_layer']['countEntries']
            );

            return $label;
        }
    ),
    'vectors'  => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/vectors.png',
        'vectors'  => true,
        'label'    => function ($row, $label) {
            $count  = \Netzmacht\Contao\Leaflet\Model\VectorModel::countBy('pid', $row['id']);
            $label .= sprintf(
                '<span class="tl_gray"> (%s %s)</span>',
                $count,
                $GLOBALS['TL_LANG']['tl_leaflet_layer']['countEntries']
            );

            return $label;
        }
    ),
    'reference' => array
    (
        'children' => false,
        'icon'     => 'system/modules/leaflet/assets/img/reference.png',
        'label'    => function ($row, $label) {
            $reference = \Netzmacht\Contao\Leaflet\Model\LayerModel::findByPk($row['reference']);

            if ($reference) {
                $label .= '<span class="tl_gray"> (' . $reference->title . ')</span>';
            }

            return $label;
        }
    )
);

/*
 * leaflet controls.
 *
 * Supported leaflet control types. Register your type for the database driven definition here.
 */
$GLOBALS['LEAFLET_CONTROLS']   = array('zoom', 'layers', 'scale', 'attribution', 'loading');


/*
 * Leaflet icons.
 *
 * Supported leaflet icon types. Register you type for the database driven definition here.
 */
$GLOBALS['LEAFLET_ICONS'] = array('image', 'div');


/*
 * The style concept is not part of the LeafletJS library. Styles are extracted from the Path options. Instead
 * of defining the style for every vector again, manage them at one place.
 *
 * The goal is to provide different style strategies. For instance a random style chooser, one which uses a color
 * range and so one.
 */
$GLOBALS['LEAFLET_STYLES'] = array('fixed');

/*
 * Leaflet vectors.
 *
 * Supported leaflet vector types. Register you type for the database driven definition here.
 */
$GLOBALS['LEAFLET_VECTORS'] = array
(
    'polyline',
    'polygon',
    'multiPolyline',
    'multiPolygon',
    'rectangle',
    'circle',
    'circleMarker'
);


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
 *
 * You don't have to define it as array if you simply add a file. Do not add |static and or media type flag to it.
 * It's getting added by default if not being in debug mode.
 */
if (!isset($GLOBALS['LEAFLET_LIBRARIES'])) {
    $GLOBALS['LEAFLET_LIBRARIES'] = array();
}
