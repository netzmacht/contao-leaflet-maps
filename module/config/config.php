<?php

/*
 * Backend module.
 */
$GLOBALS['BE_MOD']['content']['leaflet'] = array(
    'tables' => array('tl_leaflet_map'),
    'icon'   => 'system/modules/leaflet/assets/img/leaflet.png',
);

/*
 * Content elements.
 */
$GLOBALS['TL_CTE']['includes']['leaflet'] = 'Netzmacht\Contao\Leaflet\LeafletMapElement';


/*
 * Models.
 */
$GLOBALS['TL_MODELS']['tl_leaflet_map'] = 'Netzmacht\Contao\Leaflet\Model\MapModel';


/*
 * Leaflet mappers.
 *
 * Mappers do the translations between the database models and the leaflet definition.
 */
$GLOBALS['LEAFLET_MAPPERS']   = array();
$GLOBALS['LEAFLET_MAPPERS'][] = 'Netzmacht\Contao\Leaflet\Mapper\MapMapper';


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
$GLOBALS['LEAFLET_ENCODERS'][] = 'Netzmacht\Contao\Leaflet\Subscriber\EncoderSubscriber';


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
$GLOBALS['LEAFLET_ASSETS']['contao'] = array(
    'javascript' => array(
        array()
    )
);

$GLOBALS['LEAFLET_ASSETS']['leaflet'] = array(
    'css' => array(
        array('http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css', 'url')
    ),
    'javascript' => array(
        array('http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js?3', 'url')
    )
);
