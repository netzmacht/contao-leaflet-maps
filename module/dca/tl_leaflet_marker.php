<?php

$GLOBALS['TL_DCA']['tl_leaflet_marker'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_leaflet_layer',
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
            'mode'                    => 4,
            'fields'                  => array('title'),
            'flag'                    => 1,
            'headerFields'            => array('title', 'type'),
            'child_record_callback'   => array('Netzmacht\Contao\Leaflet\Dca\Marker', 'generateRow'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
        ),
        'global_operations' => array
        (
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\DevTools\Dca::createToggleIconCallback(
                    'tl_leaflet_marker',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes'    => array(
        'default' => array(
            'title'   => array('title', 'alias', 'coordinates'),
            'content' => array('tooltip', 'alt', 'addPopup'),
            'icon'    => array(':hide', 'customIcon'),
            'config'  => array(
                ':hide',
                'clickable',
                'draggable',
                'keyboard',
                'zIndexOffset',
                'opacity',
                'riseOnHover',
                'riseOffset'
            ),
            'active'  => array('active')
        ),
    ),
    'metasubpalettes' => array(
        'addPopup'   => array('popupContent'),
        'customIcon' => array(
            'icon',
            'retinaIcon',
            'iconAnchor',
            'popupAnchor',
            'iconClassName',
            'shadowImage',
            'shadowRetinaImage',
            'shadowAnchor',
        )
    ),

    'fields' => array
    (
        'id'           => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp'       => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'pid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'coordinates'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'wizard'        => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getGeocoder')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'tooltip'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['tooltip'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alt'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alt'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'addPopup'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'popupContent' => array
        (
            'label'       => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupContent'],
            'exclude'     => true,
            'inputType'   => 'text',
            'default'     => true,
            'eval'        => array('mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'),
            'explanation' => 'insertTags',
            'sql'         => "mediumtext NULL"
        ),
        'customIcon'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['customIcon'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'icon'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['icon'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'filesOnly'  => true,
                 'fieldType'  => 'radio',
                 'mandatory'  => true,
                 'tl_class'   => 'clr w50',
                 'extensions' => 'gif,png,svg,jpg'
            ),
            'sql'       => "binary(16) NULL",
        ),
        'retinaIcon'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['retinaIcon'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                 'filesOnly'  => true,
                 'fieldType'  => 'radio',
                 'mandatory'  => false,
                 'tl_class'   => 'w50',
                 'extensions' => 'gif,png,svg,jpg'
            ),
            'sql'       => "binary(16) NULL",
        ),
        'shadowImage'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['shadowImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => false,
                'tl_class'   => 'clr w50',
                'extensions' => 'gif,png,svg,jpg'
            ),
            'sql'       => "binary(16) NULL",
        ),
        'shadowRetinaImage'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['shadowRetinaImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => false,
                'tl_class'   => 'w50',
                'extensions' => 'gif,png,svg,jpg'
            ),
            'sql'       => "binary(16) NULL",
        ),
        'draggable'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['draggable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'clickable'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['clickable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'keyboard'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'zIndexOffset' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zIndexOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 0,
            'eval'      => array('mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'clr w50'),
            'sql'       => "int(4) NOT NULL default '0'"
        ),
        'iconAnchor'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['iconAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'shadowAnchor'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['shadowAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'popupAnchor'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'iconClassName'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['iconClassName'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
    ),
);
