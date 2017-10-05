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

$GLOBALS['TL_DCA']['tl_leaflet_style'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'alias' => 'unique',
            )
        ),
        'onsubmit_callback' => [
            \Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('clearCache'),
        ],
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('title'),
            'flag'                    => 1,
            'panelLayout'             => 'limit',
            'headerFields'            => array('title', 'type'),
        ),
        'label' => array
        (
            'fields'                  => array('title', 'type'),
            'format'                  => '%s <span class="tl_gray">[%s]</span>',
        ),
        'global_operations' => array
        (
            'layers' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['layersBtn'],
                'href'                => 'table=tl_leaflet_layer',
                'icon'                => 'system/modules/leaflet/assets/img/layers.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'icons' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['icons'],
                'href'                => 'table=tl_leaflet_icon',
                'icon'                => 'system/modules/leaflet/assets/img/icons.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'popups' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['popups'],
                'href'                => 'table=tl_leaflet_popup',
                'icon'                => 'system/modules/leaflet/assets/img/popup.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_style']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::stateButton(
                    'tl_leaflet_style',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_style']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'palettes' => array(
        '__selector__' => array('type')
    ),

    'metapalettes'    => array(
        'default' => array(
            'title'  => array('title', 'alias', 'type'),
            'config' => array(),
            'active' => array('active'),
        ),
        'fixed extends default' => array(
            'config' => array('stroke', 'fill'),
        ),
    ),

    'metasubpalettes' => array(
        'stroke'    => array('color', 'weight', 'opacity', 'dashArray', 'lineCap', 'lineJoin'),
        'fill'      => array('fillColor', 'fillOpacity',)
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
        'title'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_style']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
                    'tl_leaflet_style',
                    'alias',
                    ['title'],
                    \Netzmacht\Contao\Leaflet\DependencyInjection\LeafletServices::ALIAS_GENERATOR
                ),
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateAlias'),
            ),
            'eval'          => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true),
            'sql'           => "varchar(255) NULL"
        ),
        'type'                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => &$GLOBALS['LEAFLET_STYLES'],
            'reference' => &$GLOBALS['TL_LANG']['leaflet_style'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'stroke'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['stroke'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'color'                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['color'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::colorPicker()
            ),
            'eval'      => array(
                'tl_class'       => 'w50 wizard clr',
                'maxlength'      => 7,
                'decodeEntities' => true
            ),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'weight'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['weight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 5,
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NOT NULL default '5'"
        ),
        'opacity'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['opacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.5',
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "varchar(4) NOT NULL default '0.5'"
        ),
        'fill'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fill'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'clr w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'fillColor'                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fillColor'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::colorPicker()
            ),
            'eval'      => array(
                'tl_class'       => 'clr w50 wizard',
                'maxlength'      => 7,
                'decodeEntities' => true
            ),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'fillOpacity'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['fillOpacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.2',
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "varchar(4) NOT NULL default '0.2'"
        ),
        'dashArray'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['dashArray'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 32, 'tl_class' => 'w50'),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'lineCap'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineCap'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('butt', 'round', 'square', 'inherit'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineCaps'],
            'eval'      => array('mandatory' => false, 'tl_class' => 'w50 clr', 'includeBlankOption' => true, 'helpwizard'),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'lineJoin'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineJoin'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('miter', 'round', 'bevel', 'inherit'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_style']['lineJoins'],
            'eval'      => array('mandatory' => false, 'tl_class' => 'w50', 'includeBlankOption' => true, 'helpwizard'),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_style']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'sorting'   => true,
            'search'    => false,
            'flag'      => 12,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''",
            'save_callback' => [
                \Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('clearCache'),
            ],
        ),
    ),
);
