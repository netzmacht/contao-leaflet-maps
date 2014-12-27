<?php

$GLOBALS['TL_DCA']['tl_leaflet_map'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_leaflet'),
        'sql'              => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('title'),
            'flag'                    => 1
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes' => array(
        'default' => array(
            'name'      => array('title', 'alias'),
            'zoom'      => array('center', 'zoom', 'adjustExtraZoom'),
            'controls'  => array('zoomControl', 'attributionControl', 'controls'),
            'operation' => array(
                'dragging',
                'touchZoom',
                'scrollWheelZoom',
                'doubleClickZoom',
                'boxZoom',
                'tap',
                'adjustKeyboard'
            ),
            'behaviour' => array(
                'trackResize',
                'popupOnClick',
                'bounceAtZoomLimits'
            ),
            'experts' => array(
                'cache',
                'options'
            )
        ),
    ),

    'metasubpalettes' => array(
        'adjustKeyboard'  => array('keyboard', 'keyboardPanOffset', 'keyboardZoomOffset'),
        'adjustExtraZoom' => array('minZoom', 'maxZoom'),
    ),
    
    'fields' => array
    (
        'id'     => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'dragging'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['dragging'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'touchZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['touchZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'scrollWheelZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['scrollWheelZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'doubleClickZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['doubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'boxZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['boxZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'tap'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['tap'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'adjustKeyboard'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustKeyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
    ),
);
