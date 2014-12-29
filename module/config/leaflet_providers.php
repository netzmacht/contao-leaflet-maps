<?php

/*
 * Support all providers and variants which are supported by leaflet-providers.
 * See https://github.com/leaflet-extras/leaflet-providers/blob/master/leaflet-providers.js
 */

$GLOBALS['LEAFLET_TILE_PROVIDERS'] = array
(
    'OpenStreetMap' => array
    (
        'variants' => array('Mapnik', 'BlackAndWhite', 'DE', 'HOT'),
    ),
    'OpenSeaMap'    => array(),
    'Thunderforest' => array
    (
        'variants' => array('OpenCycleMap', 'Transport', 'Landscape', 'Outdoors')
    ),
    'OpenMapSurfer' => array
    (
        'variants' => array('Roads', 'AdminBounds', 'Grayscale')
    ),
    'Hydda' => array(
        'variants' => array('Full', 'Base', 'RoadsAndLabels')
    ),
    'MapQuestOpen' => array(
        'variants' => array('OSM', 'Aerial')
    ),
    'MaxBox' => array(
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
    'Acetate' => array(
        'variants' => array(
            'basemap',
            'terrain',
            'all',
            'foreground',
            'roads',
            'labels',
            'hillshading',
        )
    ),
    'FreeMapSK' => array(),
    'MtbMap'    => array(),
    'CartoDB'   => array(
        'variants' => array(
            'Positron',
            'PositronNoLabels',
            'DarkMatter',
            'DarkMatterNoLabels'
        )
    )
);
