<?php

$GLOBALS['TL_DCA']['tl_leaflet_control'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),

    // List
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes' => array(
        'default' => array(
            'name'     => array('title', 'type', 'position'),
        ),
        'zoom extends default' => array(
            'zoom' => array('zoomInText', 'zoomOutText', 'zoomInTitle', 'zoomOutTitle'),
        ),
        'layers extends default' => array(
            'layers' => array('collapsed', 'autoZIndex')
        ),
        'scale extends default' => array(
            'scale' => array('maxWidth', 'scale', 'updateWhenIdle')
        )
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'position'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['position'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('topleft', 'topright', 'bottomleft', 'bottomright'),
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomInText'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomOutText'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomInTitle'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomOutTitle'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'collapsed'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['collapsed'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'autoZIndex'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['autoZIndex'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default ''"
        ),
    ),
);
