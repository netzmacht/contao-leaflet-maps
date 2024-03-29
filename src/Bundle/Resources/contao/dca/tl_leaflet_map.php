<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2014-2022 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_leaflet_map'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'ctable'            => ['tl_leaflet_control'],
        'sql'               => [
            'keys' => [
                'id'    => 'primary',
                'alias' => 'unique',
            ],
        ],
        'onload_callback'   => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'loadLanguageFile'],
            ['netzmacht.contao_leaflet.listeners.dca.map', 'addIncompleteConfigurationWarning'],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
        ],
        'oncopy_callback' => [
            ['netzmacht.contao_leaflet.listeners.dca.map', 'copyLayerRelations'],
        ],
    ],

    'list' => [
        'sorting'           => [
            'mode'        => 1,
            'fields'      => ['title'],
            'panelLayout' => 'search,limit',
            'flag'        => 1,
        ],
        'label'             => [
            'fields' => ['title', 'alias'],
            'format' => '%s <span class="tl_gray">[%s]</span>',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'header.gif',
            ],
            'controls' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_map']['controls'],
                'href'       => 'table=tl_leaflet_control',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/control.png',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'copy'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete'   => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_map']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'metapalettes'    => [
        'default' => [
            'title'       => ['title', 'alias'],
            'zoom'        => ['center', 'zoom', 'adjustZoomExtra', 'adjustBounds', 'dynamicLoad', 'boundsPadding'],
            'locate'      => ['locate'],
            'layers'      => ['layers'],
            'interaction' => [
                'dragging',
                'touchZoom',
                'scrollWheelZoom',
                'doubleClickZoom',
                'boxZoom',
                'tap',
                'keyboard',
            ],
            'behaviour'   => [
                'zoomControl',
                'trackResize',
                'closeOnClick',
                'bounceAtZoomLimits',
            ],
            'expert'      => [
                'options',
                'cache',
            ],
        ],
    ],
    'metasubpalettes' => [
        'keyboard'        => [
            'keyboardPanOffset',
            'keyboardZoomOffset',
        ],
        'adjustZoomExtra' => [
            'minZoom',
            'maxZoom',
            'zoomSnap',
            'zoomDelta',
        ],
        'locate'          => [
            ':hide',
            'locateWatch',
            'locateSetView',
            'locateMaxZoom',
            'locateTimeout',
            'locateMaximumAge',
            'enableHighAccuracy',
        ],
        'cache'           => [
            'cacheLifeTime',
        ],
    ],

    'fields' => [
        'id'                 => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'             => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'              => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_map']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => [
                ['netzmacht.contao_toolkit.dca.listeners.alias_generator', 'handleSaveCallback'],
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateAlias'],
            ],
            'eval'          => [
                'mandatory'   => false,
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'unique'      => true,
                'doNotCopy'   => true,
                'nullIfEmpty' => true,
            ],
            'toolkit'       => [
                'alias_generator' => [
                    'factory' => 'netzmacht.contao_leaflet.definition.alias_generator.factory_default',
                    'fields'  => ['title'],
                ],
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'center'             => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_map']['center'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateCoordinates'],
            ],
            'wizard'        => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getGeocoder'],
            ],
            'eval'          => [
                'maxlength'   => 255,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'layers'             => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_map']['layers'],
            'exclude'       => true,
            'inputType'     => 'multiColumnWizard',
            'load_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.map', 'loadLayerRelations'],
            ],
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.map', 'saveLayerRelations'],
            ],
            'eval'          => [
                'multiple'       => true,
                'doNotSaveEmpty' => true,
                'tl_class'       => 'leaflet-mcw leaflet-mcw-map-layers',
                'columnFields'   => [
                    'reference' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['reference'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.map', 'getLayers'],
                        'eval'             => [
                            'mandatory'          => false,
                            'tl_class'           => 'w50',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                        'sql'              => "int(10) unsigned NOT NULL default '0'",
                    ],
                ],
                'flatArray'      => true,
            ],
            'sql'           => 'mediumblob NULL',
        ],
        'zoom'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getZoomLevels'],
            'default'          => null,
            'eval'             => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'              => 'int(4) NULL',
        ],
        'adjustZoomExtra'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustZoomExtra'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'minZoom'            => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['minZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getZoomLevels'],
            'eval'             => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'              => 'int(4) NULL',
        ],
        'maxZoom'            => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['maxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getZoomLevels'],
            'eval'             => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'              => 'int(4) NULL',
        ],
        'zoomSnap'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomSnap'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'       => 'varchar(4) NULL',
        ],
        'zoomDelta'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomDelta'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'       => 'varchar(4) NULL',
        ],
        'dragging'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['dragging'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'touchZoom'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['touchZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'scrollWheelZoom'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['scrollWheelZoom'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['1', 'center'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => [
                'tl_class'           => 'w50',
                'helpwizard'         => true,
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'][''][0],
            ],
            'sql'       => "char(6) NOT NULL default ''",
        ],
        'doubleClickZoom'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['doubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['1', 'center'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => [
                'tl_class'           => 'w50',
                'helpwizard'         => true,
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'][''][0],
            ],
            'sql'       => "char(6) NOT NULL default ''",
        ],
        'boxZoom'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['boxZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'tap'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['tap'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'trackResize'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['trackResize'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'bounceAtZoomLimits' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['bounceAtZoomLimits'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'closeOnClick'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['closeOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'keyboard'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'keyboardPanOffset'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardPanOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 80,
            'eval'      => ['mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'clr w50'],
            'sql'       => "int(4) NOT NULL default '80'",
        ],
        'keyboardZoomOffset' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardZoomOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 1,
            'eval'      => ['mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => "int(4) NOT NULL default '1'",
        ],
        'zoomControl'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'options'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['options'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => [
                'tl_class'       => 'clr lng',
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'style'          => 'min-height: 40px;',
                'rte'            => 'ace|json',
            ],
            'sql'       => 'text NULL',
        ],
        'adjustBounds'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'options'   => ['load', 'deferred'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustBoundsOptions'],
            'eval'      => ['tl_class' => 'clr w50', 'multiple' => true, 'helpwizard' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'dynamicLoad'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['dynamicLoad'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'clr w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'boundsPadding'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['boundsPadding'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'          => 32,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'       => 'varchar(32) NULL',
        ],
        'locate'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locate'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'locateWatch'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateWatch'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'locateSetView'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateSetView'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'locateTimeout'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateTimeout'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => ['maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true],
            'sql'       => 'int(9) NULL',
        ],
        'locateMaximumAge'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateMaximumAge'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => ['maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true],
            'sql'       => 'int(9) NULL',
        ],
        'enableHighAccuracy' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['enableHighAccuracy'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'locateMaxZoom'      => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateMaxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getZoomLevels'],
            'eval'             => [
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'clr w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true,
            ],
            'sql'              => 'int(4) NULL',
        ],
        'cache'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['cache'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'cacheLifeTime'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['cacheLifeTime'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 0,
            'eval'      => ['maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => "int(9) NOT NULL default '0'",
        ],
    ],
];
