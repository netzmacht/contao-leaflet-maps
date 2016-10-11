<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

/*
 * Support all providers and variants which are supported by leaflet-providers.
 * See https://github.com/leaflet-extras/leaflet-providers/blob/master/leaflet-providers.js
 */

$GLOBALS['LEAFLET_TILE_PROVIDERS'] = array
(
    'OpenStreetMap' => array
    (
        'variants' => array('Mapnik', 'BlackAndWhite', 'DE', 'France', 'HOT'),
    ),
    'OpenSeaMap'    => array(),
    'OpenTopoMap'   => array(),
    'Thunderforest' => array
    (
        'variants' => array(
            'OpenCycleMap',
            'Transport',
            'TransportDark',
            'SpinalMap',
            'Landscape',
            'Outdoors',
            'Pioneer'
        )
    ),
    'OpenMapSurfer' => array
    (
        'variants' => array('Roads', 'AdminBounds', 'Grayscale')
    ),
    'Hydda' => array(
        'variants' => array('Full', 'Base', 'RoadsAndLabels')
    ),
    'MapBox' => array(
        'class'   => 'Netzmacht\LeafletPHP\Plugins\LeafletProviders\MapBoxProvider',
        'options' => array(
            'key' => 'tile_provider_key'
        ),
    ),
    'Stamen' => array(
        'variants' => array(
            'Toner',
            'TonerBackground',
            'TonerHybrid',
            'TonerLines',
            'TonerLabels',
            'TonerLite',
            'Terrain',
            'TerrainBackground',
            'TopOSMRelief',
            'TopOSMFeatures',
            'Watercolor'
        )
    ),
    'Esri' => array(
        'variants' => array(
            'WorldStreetMap',
            'DeLorme',
            'WorldTopoMap',
            'WorldImagery',
            'WorldTerrain',
            'WorldShadedRelief',
            'WorldPhysical',
            'OceanBasemap',
            'NatGeoWorldMap',
            'WorldGrayCanvas'
        )
    ),
    'OpenWeatherMap' => array(
        'variants' => array(
            'Clouds',
            'CloudsClassic',
            'Precipitation',
            'PrecipitationClassic',
            'Rain',
            'RainClassic',
            'Pressure',
            'PressureContour',
            'Wind',
            'Temperature',
            'Snow'
        )
    ),
    'HERE' => array(
        'variants' => array(
            'normalDay',
            'normalDayCustom',
            'normalDayGrey',
            'normalDayMobile',
            'normalDayGreyMobile',
            'normalDayTransit',
            'normalDayTransitMobile',
            'normalNight',
            'normalNightMobile',
            'normalNightGrey',
            'normalNightGreyMobile',
            'basicMap',
            'mapLabels',
            'trafficFlow',
            'carnavDayGrey',
            'hybridDay',
            'hybridDayMobile',
            'pedestrianDay',
            'pedestrianNight',
            'satelliteDay',
            'terrainDay',
            'terrainDayMobile',
        ),
        'options' => array(
            'appId' => 'tile_provider_key',
            'appCode' => 'tile_provider_code',
        ),
        'fields' => array('tile_provider_key', 'tile_provider_code'),
        'class'  => 'Netzmacht\LeafletPHP\Plugins\LeafletProviders\HereProvider',
    ),
    'FreeMapSK' => array(),
    'MtbMap'    => array(),
    'CartoDB'   => array(
        'variants' => array(
            'Positron',
            'PositronNoLabels',
            'PositronOnlyLabels',
            'DarkMatter',
            'DarkMatterNoLabels',
            'DarkMatterOnlyLabels',
        )
    ),
    'HikeBike' => array(
        'variants' => array(
            'HikeBike',
            'HillShading',
        )
    ),
    'BasemapAT' => array(
        'variants' => array(
            'basemap',
            'grau',
            'overlay',
            'highdpi',
            'orthofoto',
        )
    ),
    'NASAGIBS' => array(
        'variants' => array(
            'ModisTerraTrueColorCR',
            'ModisTerraBands367CR',
            'ViirsEarthAtNight2012',
            'ModisTerraLSTDay',
            'ModisTerraSnowCover',
            'ModisTerraAOD',
            'ModisTerraChlorophyll',
        )
    ),
    'NLS' => array()
);
