<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_leaflet_marker'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'ptable'            => 'tl_leaflet_layer',
        'sql'               => [
            'keys' => [
                'id'    => 'primary',
                'pid'   => 'index',
                'alias' => 'unique',
            ],
        ],
        'onload_callback'   => [
            ['netzmacht.contao_leaflet.listeners.dca.marker', 'initialize'],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
        ],
    ],

    'list' => [
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['sorting'],
            'flag'                  => 1,
            'panelLayout'           => 'sort,filter;search,limit',
            'headerFields'          => ['title', 'type'],
            'child_record_callback' => ['netzmacht.contao_leaflet.listeners.dca.marker', 'generateRow'],
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'icons'  => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['icons'],
                'href'       => 'table=tl_leaflet_icon&id=',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/icons.png',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'popups' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popups'],
                'href'       => 'table=tl_leaflet_popup',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/popup.png',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'all'    => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [
                    'netzmacht.contao_toolkit.dca.listeners.state_button_callback',
                    'handleButtonCallback',
                ],
                'toolkit'         => [
                    'state_button' => [
                        'stateColumn' => 'active',
                    ],
                ],
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'metapalettes'    => [
        'default' => [
            'title'   => ['title', 'alias', 'coordinates'],
            'content' => ['tooltip', 'alt', 'addPopup'],
            'config'  => [
                ':hide',
                'clickable',
                'draggable',
                'keyboard',
                'zIndexOffset',
                'opacity',
                'riseOnHover',
                'riseOffset',
                'customIcon',
            ],
            'expert'  => [':hide', 'featureData'],
            'active'  => ['active', 'ignoreForBounds'],
        ],
    ],
    'metasubpalettes' => [
        'addPopup'   => ['popup', 'popupContent'],
        'customIcon' => ['icon'],
    ],

    'fields' => [
        'id'              => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'          => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting'         => [
            'sql'     => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
        ],
        'pid'             => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'           => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => [
                ['netzmacht.contao_toolkit.dca.listeners.alias_generator', 'handleSaveCallback'],
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateAlias'],
            ],
            'eval'          => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true],
            'toolkit'       => [
                'alias_generator' => [
                    'factory' => 'netzmacht.contao_leaflet.definition.alias_generator.factory_default',
                    'fields'  => ['title'],
                ],
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'coordinates'     => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateCoordinates'],
                ['netzmacht.contao_leaflet.listeners.dca.marker', 'saveCoordinates'],
            ],
            'load_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.marker', 'loadCoordinates'],
            ],
            'wizard'        => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'getGeocoder'],
            ],
            'eval'          => [
                'maxlength'      => 255,
                'tl_class'       => 'long clr',
                'nullIfEmpty'    => true,
                'doNotSaveEmpty' => true,
            ],
        ],
        'latitude'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['latitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => 'decimal(10,8) NULL',
        ],
        'longitude'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['longitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => 'decimal(11,8) NULL',
        ],
        'altitude'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['altitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => 'float NULL',
        ],
        'active'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['active'],
            'exclude'       => true,
            'filter'        => true,
            'sorting'       => true,
            'flag'          => 12,
            'inputType'     => 'checkbox',
            'eval'          => ['tl_class' => 'w50'],
            'sql'           => "char(1) NOT NULL default ''",
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
            ],
        ],
        'tooltip'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['tooltip'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alt'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alt'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'addPopup'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => ['tl_class' => 'w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'popup'           => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popup'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.marker', 'getPopups'],
            'eval'             => [
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'popupContent'    => [
            'label'       => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupContent'],
            'exclude'     => true,
            'inputType'   => 'text',
            'eval'        => ['mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql'         => 'mediumtext NULL',
        ],
        'customIcon'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['customIcon'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'icon'            => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['icon'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.marker', 'getIcons'],
            'eval'             => [
                'mandatory' => true,
                'tl_class'  => 'w50',
                'chosen'    => true,
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'draggable'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['draggable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'clickable'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['clickable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'keyboard'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'zIndexOffset'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['zIndexOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 0,
            'eval'      => ['maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'clr w50', 'nullIfEmpty' => true],
            'sql'       => 'int(5) NULL',
        ],
        'ignoreForBounds' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['ignoreForBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'featureData'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['featureData'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => [
                'tl_class'  => 'clr lng',
                'allowHtml' => true,
                'style'     => 'min-height: 40px;',
                'rte'       => 'ace|json',
            ],
            'sql'       => 'text NULL',
        ],
    ],
];
