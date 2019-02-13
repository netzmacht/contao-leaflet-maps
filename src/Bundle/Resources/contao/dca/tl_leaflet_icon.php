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

$GLOBALS['TL_DCA']['tl_leaflet_icon'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'sql'               => [
            'keys' => [
                'id'    => 'primary',
                'alias' => 'unique',
            ],
        ],
        'onsubmit_callback' => [
            ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
        ],
    ],

    'list' => [
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['title'],
            'flag'         => 1,
            'panelLayout'  => 'limit',
            'headerFields' => ['title', 'type'],
        ],
        'label'             => [
            'fields' => ['title', 'type'],
            'format' => '%s <span class="tl_gray">[%s]</span>',
        ],
        'global_operations' => [
            'layers' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['layersBtn'],
                'href'       => 'table=tl_leaflet_layer',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/layers.png',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'styles' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['styles'],
                'href'       => 'table=tl_leaflet_style',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/style.png',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'popups' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['popups'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['toggle'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        'default'               => [
            'title' => ['title', 'alias', 'type'],
        ],
        'image extends default' => [
            'config' => [
                'iconImage',
                'iconRetinaImage',
                'iconAnchor',
                'popupAnchor',
                'className',
            ],
            'shadow' => [
                'shadowImage',
                'shadowRetinaImage',
                'shadowAnchor',
            ],
            'active' => [
                'active',
            ],
        ],

        'div extends default' => [
            'config' => [
                'html',
                'iconSize',
                'iconAnchor',
                'popupAnchor',
                'className',
            ],
            'active' => [
                'active',
            ],
        ],

        'extra extends default' => [
            'config' => [
                'icon',
                'prefix',
                'shape',
                'markerColor',
                'number',
                'iconColor',
            ],
            'active' => [
                'active',
            ],
        ],
    ],

    'fields' => [
        'id'                => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'            => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'             => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
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
        'type'              => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['type'],
            'exclude'          => true,
            'inputType'        => 'select',
            'eval'             => [
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ],
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.icon', 'getIconOptions'],
            'reference'        => &$GLOBALS['TL_LANG']['leaflet_icon'],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'active'            => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['active'],
            'exclude'       => true,
            'inputType'     => 'checkbox',
            'eval'          => ['tl_class' => 'w50'],
            'sql'           => "char(1) NOT NULL default ''",
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
            ],
        ],
        'iconImage'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => [
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => true,
                'tl_class'   => 'clr',
                'extensions' => 'gif,png,svg,jpg',
            ],
            'sql'       => 'binary(16) NULL',
        ],
        'iconRetinaImage'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconRetinaImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => [
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => false,
                'tl_class'   => 'clr',
                'extensions' => 'gif,png,svg,jpg',
            ],
            'sql'       => 'binary(16) NULL',
        ],
        'shadowImage'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['shadowImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => [
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => false,
                'tl_class'   => 'clr',
                'extensions' => 'gif,png,svg,jpg',
            ],
            'sql'       => 'binary(16) NULL',
        ],
        'shadowRetinaImage' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['shadowRetinaImage'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => [
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'mandatory'  => false,
                'tl_class'   => 'clr',
                'extensions' => 'gif,png,svg,jpg',
            ],
            'sql'       => 'binary(16) NULL',
        ],
        'iconAnchor'        => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateCoordinates'],
            ],
            'eval'          => [
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'shadowAnchor'      => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['shadowAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateCoordinates'],
            ],
            'eval'          => [
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'popupAnchor'       => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['popupAnchor'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.validator', 'validateCoordinates'],
            ],
            'eval'          => [
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'           => 'varchar(255) NULL',
        ],
        'className'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'iconSize'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconSize'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'   => 64,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'       => 'varchar(64) NULL',
        ],
        'html'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['html'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => [
                'style'          => 'height:60px',
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|html',
                'tl_class'       => 'clr',
            ],
            'sql'       => 'mediumtext NULL',
        ],
        'icon'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['icon'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'   => 64,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'       => 'varchar(64) NULL',
        ],
        'prefix'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['prefix'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'   => 64,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'       => 'varchar(64) NULL',
        ],
        'shape'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['shape'],
            'exclude'   => true,
            'inputType' => 'select',
            'default'   => 'circle',
            'options'   => ['circle', 'square', 'star', 'penta'],
            'eval'      => [
                'tl_class' => 'w50',
            ],
            'sql'       => 'varchar(64) NULL',
        ],
        'iconColor'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['iconColor'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => [
                ['netzmacht.contao_toolkit.dca.listeners.color_picker', 'handleWizardCallback'],
            ],
            'eval'      => [
                'maxlength'   => 64,
                'tl_class'    => 'w50 wizard',
                'nullIfEmpty' => true,
            ],
            'sql'       => 'varchar(16) NULL',
        ],
        'markerColor'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_icon']['markerColor'],
            'exclude'   => true,
            'inputType' => 'select',
            'default'   => 'circle',
            'options'   => [
                'blue',
                'red',
                'orange-dark',
                'orange',
                'yellow',
                'blue-dark',
                'cyan',
                'purple',
                'violet',
                'pink',
                'green-dark',
                'green',
                'green-light',
                'black',
                'white',
            ],
            'eval'      => [
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ],
            'sql'       => 'varchar(16) NULL',
        ],
    ],
];
