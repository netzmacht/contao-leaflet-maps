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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('leaflet_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(
        ['leaflet_layers', 'leaflet_layer_permissions', 'leaflet_tables'],
        'leaflet_legend',
        PaletteManipulator::POSITION_APPEND
    )
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user');


$GLOBALS['TL_DCA']['tl_user']['fields']['leaflet_layers'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['leaflet_layers'],
    'exclude'   => true,
    'inputType' => 'treePicker',
    'eval'      => [
        'foreignTable'   => 'tl_leaflet_layer',
        'titleField'     => 'title',
        'searchField'    => 'title',
        'managerHref'    => 'do=leaflet_layer',
        'fieldType'      => 'checkbox',
        'selectParents'  => true,
        'multiple'       => true,
        'pickerCallback' => ['netzmacht.contao_leaflet.listeners.dca.user', 'generateLayersRowLabel']
    ],
    'sql'       => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_user']['fields']['leaflet_tables'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['leaflet_tables'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['tl_leaflet_style', 'tl_leaflet_icon', 'tl_leaflet_popup'],
    'reference' => &$GLOBALS['TL_LANG']['MOD'],
    'eval'      => [
        'multiple' => true,
    ],
    'sql'       => 'mediumblob NULL',
];

$GLOBALS['TL_DCA']['tl_user']['fields']['leaflet_layer_permissions'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['create', 'edit', 'delete', 'data'],
    'reference' => &$GLOBALS['TL_LANG']['tl_user']['leaflet_layer_permissions_options'],
    'eval'      => [
        'helpwizard' => true,
        'multiple' => true,
    ],
    'sql'       => 'mediumblob NULL',
];
