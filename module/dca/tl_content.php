<?php

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['leaflet'] = array(
    'type'      => array('type', 'headline'),
    'leaflet'   => array('leaflet_map', 'leaflet_width', 'leaflet_height'),
    'templates' => array(':hide', 'customTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(':hide', 'guests', 'cssID', 'space'),
    'invisible' => array(':hide', 'invisible', 'start', 'start')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['leaflet_map'] = array(
    'label'            => &$GLOBALS['TL_LANG']['tl_content']['leaflet_map'],
    'inputType'        => 'select',
    'exclude'          => true,
    'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Content', 'getMaps'),
    'eval'             => array(
        'tl_class' => 'w50',
        'chosen'   => true,
    ),
    'sql'              => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['leaflet_width'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['leaflet_width'],
    'inputType' => 'inputUnit',
    'options'   => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
    'search'    => false,
    'exclude'   => true,
    'eval'      => array('rgxp' => 'digit', 'tl_class' => 'clr w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['leaflet_height'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['leaflet_height'],
    'inputType' => 'inputUnit',
    'options'   => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
    'search'    => false,
    'exclude'   => true,
    'eval'      => array('rgxp' => 'digit', 'tl_class' => 'w50'),
    'sql'       => "varchar(64) NOT NULL default ''"
);
