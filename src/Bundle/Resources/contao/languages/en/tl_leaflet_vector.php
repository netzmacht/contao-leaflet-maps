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

$GLOBALS['TL_LANG']['tl_leaflet_vector']['title_legend']  = 'Title and type';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['active_legend'] = 'Activation';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['config_legend'] = 'Configuration';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['data_legend']   = 'Vector data';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popup_legend']  = 'Popup';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['expert_legend'] = 'Expert settings';


$GLOBALS['TL_LANG']['tl_leaflet_vector']['new'][0]    = 'Create vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['new'][1]    = 'Create new vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['edit'][0]   = 'Edit vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['edit'][1]   = 'Edit vector ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['copy'][0]   = 'Copy vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['copy'][1]   = 'Copy vector ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['delete'][0] = 'Delete vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['delete'][1] = 'Delete vector ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['show'][0]   = 'Show details';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['show'][1]   = 'Show vector ID %s details';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['cut'][0]    = 'Move vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['cut'][1]    = 'Move vector ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['toggle'][0] = 'Toggle activation';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['toggle'][1] = 'Toggle vector ID %s activation';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['styles'][0] = 'Manage styles';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['styles'][1] = 'Manage vector styles';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popups'][0] = 'Manage popups';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popups'][1] = 'Manage popups icons';

$GLOBALS['TL_LANG']['tl_leaflet_vector']['title'][0]           = 'Title';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['title'][1]           = 'Title of the vector.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['alias'][0]           = 'Alias';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['alias'][1]           = 'Alias of the vector.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'][0]            = 'Type';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'][1]            = 'Choose the vector type.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'][0]            = 'Type';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'][1]            = 'Choose the vector type.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'][0]            = 'Vector data';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'][1]            = 'Define each coordinate of the vector on a line. Each line is a comma separated value (latitude, longitude [, altitude]).';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['multiData'][0]       = 'Multi data';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['multiData'][1]       = 'Define coordinates of each vector in a new textarea.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['addPopup'][0]        = 'Add popup';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['addPopup'][1]        = 'Add a popup to the vector.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popup'][0]           = 'Popup';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popup'][1]           = 'Choose a popup which options should be used.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popupContent'][0]    = 'Popup content';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['popupContent'][1]    = 'Content of the popup. Insert tags are replaced.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['clickable'][0]       = 'Clickable';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['clickable'][1]       = 'If deactivated, the vector will not emit mouse events and will act as a part of the underlying map.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['active'][0]          = 'Activate vector';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['active'][1]          = 'Only activated vector are rendered on the map.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['className'][0]       = 'Class name';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['className'][1]       = 'Custom class name set on an element.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['coordinates'][0]     = 'Coordinates';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['coordinates'][1]     = 'Coordinates of the vector as comma separated value (Latitude, longitude [, altitude]).';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['radius'][0]          = 'Radius';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['radius'][1]          = 'Radius of the element. For a circle its defined in meters, for a circle marker in pixels.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['bounds'][0]          = 'Bounds';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['bounds'][1]          = 'Each field defines a corner of the bounds as comma separated value (Latitude, longitude [, altitude]).';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['style'][0]           = 'Style';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['style'][1]           = 'Choose a style. If none defined, the default style of leaflet is used.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['ignoreForBounds'][0] = 'Exclude from bounds';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['ignoreForBounds'][1] = 'Do not include this item in the bounds calculation.';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['featureData'][0]     = 'Feature data';
$GLOBALS['TL_LANG']['tl_leaflet_vector']['featureData'][1]     = 'The marker is transferred as GeoJSON feature. These data is passed as <em>feature.properties.data</em>.';
