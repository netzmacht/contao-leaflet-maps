<?php

$GLOBALS['TL_DCA']['tl_leaflet_layer'] = array
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
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 5,
            'fields'                  => array('title'),
            'flag'                    => 1,
            'icon'                    => 'system/modules/leaflet/assets/img/layers.png',
            'paste_button_callback'   => array('Netzmacht\Contao\Leaflet\Dca\Layer', 'getPasteButtons'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s'
        ),
        'global_operations' => array
        (
            'map' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['map'],
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
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes' => array(
        'default' => array(
            'title'       => array('title', 'alias', 'type'),
        ),
    ),

    'metasubselectpalettes' => array(
        'type' => array(
            'provider' => array('tile_provider', 'tile_provider_variant')
        ),
        'tile_provider' => array(
            'MapBox' => array('tile_provider_key'),
            'HERE'   => array('tile_provider_key', 'tile_provider_code'),
        ),
    ),
    
    'fields' => array
    (
        'id'     => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'type'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => array_keys($GLOBALS['LEAFLET_LAYERS']),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'tile_provider' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => array_keys($GLOBALS['LEAFLET_TILE_PROVIDERS']),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'tile_provider_variant' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_variant'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'submitOnChange'     => true,
                'chosen'             => false,
            ),
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Layer', 'getVariants'),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'tile_provider_key'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_key'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'tile_provider_code'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_code'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
    )
);
