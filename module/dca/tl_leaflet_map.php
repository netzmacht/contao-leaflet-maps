<?php

$GLOBALS['TL_DCA']['tl_leaflet_map'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_leaflet_control'),
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
            'flag'                    => 1,
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'layers' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['layers'],
                'href'                => 'table=tl_leaflet_layer',
                'icon'                => 'system/modules/leaflet/assets/img/layers.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'controls' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['controls'],
                'href'                => 'table=tl_leaflet_control',
                'icon'                => 'system/modules/leaflet/assets/img/control.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
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
            'title'       => array('title', 'alias'),
            'zoom'        => array('center', 'zoom', 'adjustZoomExtra'),
            'controls'    => array('zoomControl', 'controls'),
            'layers'      => array('layers'),
            'interaction' => array(
                'dragging',
                'touchZoom',
                'scrollWheelZoom',
                'doubleClickZoom',
                'boxZoom',
                'tap',
                'keyboard'
            ),
            'behaviour'   => array(
                'trackResize',
                'closeOnClick',
                'bounceAtZoomLimits'
            ),
            'expert'      => array(
                'options',
            )
        ),
    ),

    'metasubpalettes' => array(
        'keyboard'        => array('keyboardPanOffset', 'keyboardZoomOffset'),
        'adjustZoomExtra' => array('minZoom', 'maxZoom'),
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
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'center'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['center'],
            'exclude'   => true,
            'inputType' => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'wizard' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getGeocoder')
            ),
            'eval' => array(
                'maxlength' => 255,
                'tl_class' => 'long clr',
                'nullIfEmpty' => true,
            ),
            'sql'       => "varchar(255) NULL"
        ),
        'layers' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['layers'],
            'exclude'          => true,
            'inputType'        => 'checkboxWizard',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getLayers'),
            'default'          => '',
            'eval'             => array(
                'multiple'           => true,
            ),
            'sql'              => "mediumblob NULL"
        ),
        'zoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getZoomLevels'),
            'default'          => '',
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'adjustZoomExtra'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustZoomExtra'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'minZoom'  => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['minZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'maxZoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['maxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
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
            'inputType' => 'select',
            'options'   => array('1', '', 'center'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'helpwizard' => true,),
            'sql'       => "char(6) NOT NULL default ''"
        ),
        'doubleClickZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['doubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('1', '', 'center'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'helpwizard' => true,),
            'sql'       => "char(6) NOT NULL default ''"
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
        'trackResize'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['trackResize'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'bounceAtZoomLimits'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['bounceAtZoomLimits'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'closeOnClick'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['closeOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'keyboard'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'keyboardPanOffset'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardPanOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 80,
            'eval'      => array('mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'clr w50'),
            'sql'       => "int(4) NOT NULL default '80'"
        ),
        'keyboardZoomOffset'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardZoomOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 1,
            'eval'      => array('mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NOT NULL default '1'"
        ),
        'zoomControl'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'options'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['options'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'default'   => true,
            'eval'      => array('tl_class' => 'clr lng', 'allowHtml'=>true, 'style' => 'min-height: 40px;'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
    ),
);
