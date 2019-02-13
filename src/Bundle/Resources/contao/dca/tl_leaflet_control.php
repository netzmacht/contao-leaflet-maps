<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_leaflet_control'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'ptable'            => 'tl_leaflet_map',
        'sql'               => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
        'onload_callback'   => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'loadLanguageFile'],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
        ],
    ],

    // List configuration
    'list'   => [
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['sorting'],
            'headerFields'          => ['title'],
            'flag'                  => 1,
            'sorting'               => 2,
            'panelLayout'           => 'filter,sort;search,limit',
            'child_record_callback' => ['netzmacht.contao_leaflet.listeners.dca.control', 'generateRow'],
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
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
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_control']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'header.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_control']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_control']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_control']['toggle'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_control']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        'default'                     => [
            'name'   => ['title', 'alias', 'type', 'position'],
            'config' => [],
            'active' => ['active'],
        ],
        'zoom extends default'        => [
            'config' => ['zoomInText', 'zoomOutText', 'zoomInTitle', 'zoomOutTitle'],
        ],
        'layers extends default'      => [
            'config' => ['layers', 'collapsed', 'autoZIndex'],
        ],
        'scale extends default'       => [
            'config' => ['maxWidth', 'metric', 'imperial', 'updateWhenIdle'],
        ],
        'attribution extends default' => [
            'config' => ['attributions', 'prefix', 'disableDefault'],
        ],
        'loading extends default'     => [
            'config' => ['separate', 'zoomControl', 'spinjs'],
        ],
        'fullscreen extends default'  => [
            'config' => ['buttonTitle', 'separate', 'simulateFullScreen'],
        ],
    ],

    'metasubpalettes' => [
        'spinjs' => ['spin'],
    ],

    'fields' => [
        'id'                 => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid'                => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'             => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting'            => [
            'sql'     => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
        ],
        'title'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'sorting'   => true,
            'search'    => true,
            'flag'      => 1,
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'              => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_control']['alias'],
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
                'nullIfEmpty' => true,
                'doNotCopy'   => true,
            ],
            'toolkit'       => [
                'alias_generator' => [
                    'factory' => 'netzmacht.contao_leaflet.definition.alias_generator.factory_parent',
                    'fields'  => ['title'],
                ],
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'type'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_control']['type'],
            'exclude'          => true,
            'inputType'        => 'select',
            'filter'           => true,
            'sorting'          => true,
            'eval'             => [
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ],
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.control', 'getControlTypes'],
            'reference'        => &$GLOBALS['TL_LANG']['leaflet_control'],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'position'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['position'],
            'exclude'   => true,
            'inputType' => 'select',
            'filter'    => true,
            'sorting'   => true,
            'options'   => ['topleft', 'topright', 'bottomleft', 'bottomright'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_control'],
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'helpwizard' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'active'             => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_control']['active'],
            'exclude'       => true,
            'inputType'     => 'checkbox',
            'filter'        => true,
            'eval'          => ['tl_class' => 'w50'],
            'sql'           => "char(1) NOT NULL default ''",
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
            ],
        ],
        'zoomInText'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'zoomOutText'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'zoomInTitle'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInTitle'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'zoomOutTitle'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomOutTitle'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'collapsed'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['collapsed'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'autoZIndex'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['autoZIndex'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'layers'             => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layers'],
            'exclude'       => true,
            'inputType'     => 'multiColumnWizard',
            'load_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.control', 'loadLayerRelations'],
            ],
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.control', 'saveLayerRelations'],
            ],
            'eval'          => [
                'tl_class'     => 'leaflet-mcw leaflet-mcw-control-layers',
                'columnFields' => [
                    'layer' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layer'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.control', 'getLayers'],
                        'eval'             => [
                            'style'              => 'width: 300px',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                        ],
                    ],
                    'mode'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layerMode'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'options'   => ['base', 'overlay'],
                        'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_control'],
                        'eval'      => [
                            'style'      => 'width: 200px',
                            'helpwizard' => true,
                        ],
                    ],
                ],
            ],
            'sql'           => 'mediumblob NULL',
        ],
        'maxWidth'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['maxWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 100,
            'eval'      => ['tl_class' => 'w50', 'rgxp' => 'digit'],
            'sql'       => "int(5) NOT NULL default '100'",
        ],
        'metric'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['metric'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'imperial'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['imperial'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'updateWhenIdle'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['updateWhenIdle'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'prefix'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['prefix'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'attributions'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['attributions'],
            'exclude'   => true,
            'inputType' => 'listWizard',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'clr', 'allowHtml' => true],
            'sql'       => 'mediumblob NULL',
        ],
        'separate'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['separate'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'zoomControl'        => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomControl'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.control', 'getZoomControls'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_leaflet_control'],
            'eval'             => [
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ],
            'sql'              => "varchar(255) NOT NULL default ''",
        ],
        'spinjs'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['spinjs'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'spin'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['spin'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => [
                'style'          => 'height:60px',
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|json',
                'tl_class'       => 'clr',
            ],
            'sql'       => 'mediumtext NULL',
        ],
        'simulateFullScreen' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['simulateFullScreen'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'buttonTitle'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['buttonTitle'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'disableDefault'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['disableDefault'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
    ],
];
