<?php

$GLOBALS['TL_DCA']['tl_leaflet_icon'] = array
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
            'mode'                    => 1,
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
            'map' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['map'],
                'href'                => 'table=tl_leaflet_map',
                'icon'                => 'system/modules/leaflet/assets/img/leaflet.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="m"'
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\DevTools\Dca::createToggleIconCallback(
                    'tl_leaflet_icon',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes'    => array(
        'default' => array(
            'title'   => array('title', 'alias', 'type'),
            'content' => array('tooltip', 'alt',),
            'config'  => array(
                ':hide',
                'iconUrl',
                'iconRetinaUrl',
                'iconAnchor',
                'popupAnchor',
                'iconClassName',
                'shadowImage',
                'shadowRetinaImage',
                'shadowAnchor',
            ),
            'active'  => array('active')
        ),
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'iconUrl'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['iconUrl'],
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
        'iconRetinaUrl'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['iconRetinaUrl'],
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
        'iconAnchor'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconAnchor'],
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
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['shadowAnchor'],
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
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['popupAnchor'],
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
        'className'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
    ),
);
