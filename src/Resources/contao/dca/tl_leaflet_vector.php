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

$GLOBALS['TL_DCA']['tl_leaflet_vector'] = [
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
            ['netzmacht.contao_leaflet_maps.listeners.dca.leaflet', 'loadLanguageFile'],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_leaflet_maps.listeners.dca.leaflet', 'clearCache'],
        ],
    ],

    'list' => [
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['sorting'],
            'flag'                  => 1,
            'panelLayout'           => 'sort,filter;search,limit',
            'headerFields'          => ['title', 'type'],
            'child_record_callback' => ['netzmacht.contao_leaflet_maps.listeners.dca.vector', 'generateRow'],
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'styles' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['styles'],
                'href'       => 'table=tl_leaflet_style',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/style.png',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'popups' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popups'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'cut'    => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => [
                    'netzmacht.contao_toolkit.dca.listeners.state_button_callback',
                    'handleButtonCallback',
                ],
                'toolkit'         => [
                    'state_button' => [
                        'stateColumn' => ['active'],
                    ],
                ],
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes'    => [
        'default' => [
            'title'  => ['title', 'alias', 'type'],
            'data'   => [],
            'popup'  => [':hide', 'addPopup'],
            'config' => [':hide', 'style', 'className', 'clickable'],
            'expert' => [':hide', 'featureData'],
            'active' => ['active', 'ignoreForBounds'],
        ],

        'polyline extends default' => [
            '+config' => ['smoothFactor', 'noClip'],
            'data'    => ['data'],
        ],

        'multiPolyline extends polyline' => [
            'data' => ['multiData'],
        ],

        'polygon extends polyline' => [],

        'multiPolygon extends multiPolyline' => [
        ],

        'rectangle extends polygon' => [
            'data' => ['bounds'],
        ],

        'circle extends default' => [
            '+data' => ['coordinates', 'radius'],
        ],

        'circleMarker extends circle' => [],
    ],
    'metasubpalettes' => [
        'addPopup' => ['popup', 'popupContent'],
    ],

    'fields' => [
        'id'              => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'          => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'pid'             => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting'         => [
            'sql'     => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
        ],
        'title'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'filter'    => false,
            'sorting'   => true,
            'search'    => true,
            'flag'      => 1,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'           => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => [
                ['netzmacht.contao_toolkit.dca.listeners.alias_generator', 'handleSaveCallback'],
                ['netzmacht.contao_leaflet_maps.listeners.dca.validator', 'validateAlias'],
            ],
            'eval'          => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true],
            'toolkit'       => [
                'alias_generator' => [
                    'factory' => 'netzmacht.contao_leaflet_maps.definition.alias_generator.factory_default',
                    'fields'  => ['title'],
                ],
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'type'            => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'],
            'exclude'          => true,
            'inputType'        => 'select',
            'filter'           => true,
            'sorting'          => true,
            'search'           => false,
            'flag'             => 1,
            'eval'             => [
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ],
            'options_callback' => ['netzmacht.contao_leaflet_maps.listeners.dca.vector', 'getVectorOptions'],
            'reference'        => &$GLOBALS['TL_LANG']['leaflet_vector'],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'active'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['active'],
            'exclude'       => true,
            'inputType'     => 'checkbox',
            'filter'        => true,
            'sorting'       => true,
            'search'        => false,
            'flag'          => 12,
            'eval'          => ['tl_class' => 'w50'],
            'sql'           => "char(1) NOT NULL default ''",
            'save_callback' => [
                ['netzmacht.contao_leaflet_maps.listeners.dca.leaflet', 'clearCache'],
            ],
        ],
        'addPopup'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => ['tl_class' => 'w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'popup'           => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popup'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['Netzmacht\Contao\Leaflet\Listener\Dca\MarkerDcaListener', 'getPopups'],
            'eval'             => [
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'popupContent'    => [
            'label'       => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popupContent'],
            'exclude'     => true,
            'inputType'   => 'text',
            'eval'        => ['mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql'         => 'mediumtext NULL',
        ],
        'style'           => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['style'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet_maps.listeners.dca.vector', 'getStyles'],
            'eval'             => [
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'clickable'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['clickable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'm12 w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'className'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'coordinates'     => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet_maps.listeners.dca.validator', 'validateCoordinates'],
            ],
            'wizard'        => [
                ['netzmacht.contao_leaflet_maps.listeners.dca.leaflet', 'getGeocoder'],
            ],
            'eval'          => [
                'maxlength'   => 255,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
                'mandatory'   => true,
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'radius'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['radius'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 5,
            'eval'      => ['mandatory' => false, 'maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'clr w50'],
            'sql'       => "int(10) NOT NULL default '5'",
        ],
        'data'            => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'],
            'inputType'     => 'textarea',
            'search'        => false,
            'eval'          => ['mandatory' => true, 'alwaysSave' => true],
            'save_callback' => [
                ['netzmacht.contao_leaflet_maps.listeners.dca.validator', 'validateMultipleCoordinates'],
            ],
            'sql'           => 'longblob NULL',
        ],
        'multiData'       => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['multiData'],
            'inputType'     => 'multiColumnWizard',
            'search'        => false,
            'eval'          => [
                'mandatory'    => true,
                'alwaysSave'   => true,
                'flatArray'    => true,
                'columnFields' => [
                    'data' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'],
                        'inputType' => 'textarea',
                        'search'    => false,
                        'eval'      => ['alwaysSave' => true, 'style' => 'width:600px'],
                    ],
                ],
            ],
            'save_callback' => [
                ['netzmacht.contao_leaflet_maps.listeners.dca.validator', 'validateMultipleCoordinateSets'],
            ],
            'sql'           => 'longblob NULL',
        ],
        'bounds'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['bounds'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [],
            'eval'          => [
                'maxlength'   => 255,
                'multiple'    => true,
                'size'        => 2,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
            ],
            'sql'           => 'mediumblob NULL',
        ],
        'ignoreForBounds' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['ignoreForBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'featureData'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['featureData'],
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
