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

$GLOBALS['TL_DCA']['tl_leaflet_style'] = [
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
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_style']['layersBtn'],
                'href'       => 'table=tl_leaflet_layer',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/layers.png',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'icons'  => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_style']['icons'],
                'href'       => 'table=tl_leaflet_icon',
                'icon'       => 'bundles/netzmachtcontaoleaflet/img/icons.png',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'popups' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_style']['popups'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_style']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_style']['toggle'],
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
                'label' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['show'],
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
            'title'  => ['title', 'alias', 'type'],
            'config' => [],
            'active' => ['active'],
        ],
        'fixed extends default' => [
            'config' => ['stroke', 'fill'],
        ],
    ],

    'metasubpalettes' => [
        'stroke' => ['color', 'weight', 'opacity', 'dashArray', 'lineCap', 'lineJoin'],
        'fill'   => ['fillColor', 'fillOpacity',],
    ],

    'fields' => [
        'id'          => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp'      => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'       => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_style']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
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
        'type'        => [
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_style']['type'],
            'exclude'          => true,
            'inputType'        => 'select',
            'eval'             => [
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ],
            'options_callback' => ['netzmacht.contao_leaflet.listeners.dca.style', 'getStyleOptions'],
            'reference'        => &$GLOBALS['TL_LANG']['leaflet_style'],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'stroke'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['stroke'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'color'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['color'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => [
                ['netzmacht.contao_toolkit.dca.listeners.color_picker', 'handleWizardCallback'],
            ],
            'eval'      => [
                'tl_class'       => 'w50 wizard clr',
                'maxlength'      => 7,
                'decodeEntities' => true,
            ],
            'sql'       => "varchar(8) NOT NULL default ''",
        ],
        'weight'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['weight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 5,
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => "int(4) NOT NULL default '5'",
        ],
        'opacity'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['opacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.5',
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => "varchar(4) NOT NULL default '0.5'",
        ],
        'fill'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fill'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'fillColor'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fillColor'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => [
                ['netzmacht.contao_toolkit.dca.listeners.color_picker', 'handleWizardCallback'],
            ],
            'eval'      => [
                'tl_class'       => 'clr w50 wizard',
                'maxlength'      => 7,
                'decodeEntities' => true,
            ],
            'sql'       => "varchar(8) NOT NULL default ''",
        ],
        'fillOpacity' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fillOpacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.2',
            'eval'      => ['mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'       => "varchar(4) NOT NULL default '0.2'",
        ],
        'dashArray'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['dashArray'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 32, 'tl_class' => 'w50'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'lineCap'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineCap'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['butt', 'round', 'square', 'inherit'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineCaps'],
            'eval'      => [
                'mandatory'          => false,
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true,
                'helpwizard'         => true,
            ],
            'sql'       => "varchar(8) NOT NULL default ''",
        ],
        'lineJoin'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineJoin'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['miter', 'round', 'bevel', 'inherit'],
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineJoins'],
            'eval'      => ['mandatory' => false, 'tl_class' => 'w50', 'includeBlankOption' => true, 'helpwizard'],
            'sql'       => "varchar(8) NOT NULL default ''",
        ],
        'active'      => [
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_style']['active'],
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
