<?php

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['leaflet'] = array(
    'type'      => array('type', 'headline'),
    'leaflet'   => array('leaflet_map'),
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
