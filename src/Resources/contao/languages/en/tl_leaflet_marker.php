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

$GLOBALS['TL_LANG']['tl_leaflet_marker']['title_legend']   = 'Title and type';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['content_legend'] = 'Content';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['config_legend']  = 'Configuration';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['active_legend']  = 'Activation';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['expert_legend']      = 'Expert settings';


$GLOBALS['TL_LANG']['tl_leaflet_marker']['new'][0]    = 'Create marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['new'][1]    = 'Create new marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['edit'][0]   = 'Edit marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['edit'][1]   = 'Edit marker ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['copy'][0]   = 'Copy marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['copy'][1]   = 'Copy marker ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['delete'][0] = 'Delete marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['delete'][1] = 'Delete marker ID %s';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['show'][0]   = 'Show details';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['show'][1]   = 'Show marker ID %s details';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['toggle'][0] = 'Toggle activation';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['toggle'][1] = 'Toggle marker ID %s activation';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['icons'][0]  = 'Manage icons';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['icons'][1]  = 'Manage marker icons.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popups'][0] = 'Manage popups';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popups'][1] = 'Manage popup options.';

$GLOBALS['TL_LANG']['tl_leaflet_marker']['title'][0]           = 'Title';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['title'][1]           = 'Title of the map.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['alias'][0]           = 'Alias';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['alias'][1]           = 'Alias of the map.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['coordinates'][0]     = 'Coordinates';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['coordinates'][1]     = 'Coordinates of the marker as comma separated value (Latitude, longitude [, altitude]).';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['tooltip'][0]         = 'Tooltip';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['tooltip'][1]         = 'Marker tooltip rendered as title attribute.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['alt'][0]             = 'Alternative text';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['alt'][1]             = 'Text for the alt attribute of the icon image (useful for accessibility).';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['addPopup'][0]        = 'Add popup';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['addPopup'][1]        = 'Add a popup for the marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popup'][0]           = 'Popup';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popup'][1]           = 'Choose a popup which options should be applied.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupContent'][0]    = 'Popup content';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupContent'][1]    = 'Content of the popup. Insert tags are replaced.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['clickable'][0]       = 'Clickable';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['clickable'][1]       = 'If deactivated, the marker will not emit mouse events and will act as a part of the underlying map.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['draggable'][0]       = 'Draggable';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['draggable'][1]       = 'Whether the marker is draggable with mouse/touch or not.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['keyboard'][0]        = 'Keyboard navigation';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['keyboard'][1]        = 'Whether the marker can be tabbed to with a keyboard and clicked by pressing enter.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['zIndexOffset'][0]    = 'ZIndex offset';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['zIndexOffset'][1]    = 'By default, marker images zIndex is set automatically based on its latitude. Use this option if you want to put the marker on top of all others (or below), specifying a high value like 1000 (or high negative value, respectively).';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['customIcon'][0]      = 'Custom icon';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['customIcon'][1]      = 'Use a custom icon.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['icon'][0]            = 'Icon';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['icon'][1]            = 'Select a custom icon.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['active'][0]          = 'Activate marker';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['active'][1]          = 'Only activated markers are rendered on the map.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['ignoreForBounds'][0] = 'Exclude from bounds';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['ignoreForBounds'][1] = 'Do not include this item in the bounds calculation.';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['featureData'][0]     = 'Feature data';
$GLOBALS['TL_LANG']['tl_leaflet_marker']['featureData'][1]     = 'The marker is transferred as GeoJSON feature. These data is passed as <em>feature.properties.data</em>.';
