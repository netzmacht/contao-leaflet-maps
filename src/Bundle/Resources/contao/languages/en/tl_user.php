<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_user']['leaflet_legend'] = 'Leaflet Maps Permissions';

$GLOBALS['TL_LANG']['tl_user']['leaflet_layers'][0]            = 'Allowed map layers';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layers'][1]            = 'Please choose the allowed map layers.';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions'][0] = 'Layer permissions';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions'][1] = 'Please choose which permissions are allowed for the map layer.';
$GLOBALS['TL_LANG']['tl_user']['leaflet_tables'][0]            = 'Allows map tables';
$GLOBALS['TL_LANG']['tl_user']['leaflet_tables'][1]            = 'Please choose which tables are allowed.';

$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['create'][0] = 'Create layers';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['create'][1] = 'Grant permission to create new layers.';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['edit'][0]   = 'Edit layers';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['edit'][1]   = 'Grant permission to edit layers.';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['delete'][0] = 'Delete layers';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['delete'][1] = 'Grant permission to delete layers.';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['data'][0]   = 'Edit data';
$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options']['data'][1]   = 'Grant permission to edit data of a layer.';
