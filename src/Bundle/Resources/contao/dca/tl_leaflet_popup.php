<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2014-2022 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_leaflet_popup'] = [
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
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['layersBtn'],
                'href'       => 'table=tl_leaflet_layer',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/layers.png',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'styles' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['styles'],
                'href'       => 'table=tl_leaflet_style',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/style.png',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'icons'  => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['icons'],
                'href'       => 'table=tl_leaflet_icon',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/icons.png',
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '')
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['toggle'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['type'],
    ],

    'metapalettes' => [
        'default' => [
            'title'  => ['title', 'alias'],
            'size'   => ['maxWidth', 'minWidth', 'maxHeight'],
            'config' => [
                ':hide',
                'closeButton',
                'keepInView',
                'closeOnClick',
                'zoomAnimation',
                'offset',
                'className',
                'autoPan',
            ],
            'active' => ['active'],
        ],
    ],

    'metasubpalettes' => [
        'autoPan' => ['autoPanPadding'],
    ],

    'fields' => [
        'id'             => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'         => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'          => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['alias'],
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
        'maxWidth'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['maxWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => 'int(4) NULL',
        ],
        'minWidth'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['minWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => 'int(4) NULL',
        ],
        'maxHeight'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['maxHeight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => 'int(4) NULL',
        ],
        'autoPan'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['autoPan'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50 m12', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'keepInView'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['keepInView'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'closeButton'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['closeButton'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'offset'         => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['offset'],
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
        'autoPanPadding' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['autoPanPadding'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
                'multiple'    => true,
                'size'        => 2,
            ],
            'sql'       => 'varchar(255) NULL',
        ],
        'zoomAnimation'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['zoomAnimation'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'closeOnClick'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['closeOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => false],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'className'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'active'         => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['active'],
            'exclude'       => true,
            'inputType'     => 'checkbox',
            'filter'        => true,
            'sorting'       => true,
            'search'        => false,
            'flag'          => 12,
            'eval'          => ['tl_class' => 'w50'],
            'sql'           => "char(1) NOT NULL default ''",
            'save_callback' => [
                ['netzmacht.contao_leaflet.listeners.dca.leaflet', 'clearCache'],
            ],
        ],
    ],
];
