<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_module']['metapalettes']['leaflet'] = array(
    'type'      => array('name', 'type', 'headline'),
    'leaflet'   => array('leaflet_map', 'leaflet_width', 'leaflet_height'),
    'templates' => array(':hide', 'customTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(':hide', 'guests', 'cssID', 'space'),
    'invisible' => array(':hide', 'invisible', 'start', 'start')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_map'] = array(
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['leaflet_map'],
    'inputType'        => 'select',
    'exclude'          => true,
    'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\FrontendIntegration', 'getMaps'),
    'wizard'           => array(
        array('Netzmacht\Contao\Leaflet\Dca\FrontendIntegration', 'getEditMapLink'),
    ),
    'eval'             => array(
        'tl_class' => 'w50 wizard',
        'chosen'   => true,
    ),
    'sql'              => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_width'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['leaflet_width'],
    'inputType' => 'inputUnit',
    'options'   => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
    'search'    => false,
    'exclude'   => true,
    'eval'      => array('rgxp' => 'digit', 'tl_class' => 'clr w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_module']['fields']['leaflet_height'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['leaflet_height'],
    'inputType' => 'inputUnit',
    'options'   => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
    'search'    => false,
    'exclude'   => true,
    'eval'      => array('rgxp' => 'digit', 'tl_class' => 'w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);
