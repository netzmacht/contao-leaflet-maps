<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_marker'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_leaflet_layer',
        'sql'              => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'pid'   => 'index',
                'alias' => 'unique',
            )
        ),
        'onload_callback' => array(
            function() {
                \Controller::loadLanguageFile('leaflet');
            }
        ),
        'onsubmit_callback' => [
            \Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('clearCache'),
        ],
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('sorting'),
            'flag'                    => 1,
            'panelLayout'             => 'sort,filter;search,limit',
            'headerFields'            => array('title', 'type'),
            'child_record_callback'   => array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'generateRow'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
        ),
        'global_operations' => array
        (
            'icons' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['icons'],
                'href'                => 'table=tl_leaflet_icon&id=',
                'icon'                => 'system/modules/leaflet/assets/img/icons.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'popups' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popups'],
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::stateButton(
                    'tl_leaflet_marker',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes'    => array(
        'default' => array(
            'title'   => array('title', 'alias', 'coordinates'),
            'content' => array('tooltip', 'alt', 'addPopup'),
            'config'  => array(
                ':hide',
                'clickable',
                'draggable',
                'keyboard',
                'zIndexOffset',
                'opacity',
                'riseOnHover',
                'riseOffset',
                'customIcon',
            ),
            'expert'  => array(':hide', 'featureData'),
            'active'  => array('active', 'ignoreForBounds')
        ),
    ),
    'metasubpalettes' => array(
        'addPopup'   => array('popup', 'popupContent'),
        'customIcon' => array('icon')
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
        'sorting'               => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'sorting'   => true,
        ),
        'pid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
                    'tl_leaflet_marker',
                    'alias',
                    ['title'],
                    \Netzmacht\Contao\Leaflet\DependencyInjection\LeafletServices::ALIAS_GENERATOR
                ),
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateAlias'),
            ),
            'eval'          => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true),
            'sql'           => "varchar(255) NULL"
        ),
        'coordinates'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateCoordinates'),
                array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'saveCoordinates')
            ),
            'load_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'loadCoordinates')
            ),
            'wizard'        => array(
                Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('getGeocoder')
            ),
            'eval'          => array(
                'maxlength'      => 255,
                'tl_class'       => 'long clr',
                'nullIfEmpty'    => true,
                'doNotSaveEmpty' => true,
            ),
        ),
        'latitude'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['latitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "decimal(10,8) NULL"
        ),
        'longitude'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['longitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "decimal(11,8) NULL"
        ),
        'altitude'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['altitude'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "float NULL"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['active'],
            'exclude'   => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => 12,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''",
            'save_callback' => [
                \Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('clearCache'),
            ],
        ),
        'tooltip'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['tooltip'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alt'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['alt'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'addPopup'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'popup'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popup'],
            'exclude'   => true,
            'inputType' => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'getPopups'),
            'eval'      => array(
                'mandatory'          => false,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ),
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ),
        'popupContent' => array
        (
            'label'       => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['popupContent'],
            'exclude'     => true,
            'inputType'   => 'text',
            'eval'        => array('mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'),
            'explanation' => 'insertTags',
            'sql'         => "mediumtext NULL"
        ),
        'customIcon'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['customIcon'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'clr w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'icon'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['icon'],
            'exclude'   => true,
            'inputType' => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'getIcons'),
            'eval'      => array(
                 'mandatory'  => true,
                 'tl_class'   => 'w50',
                 'chosen'     => true,
            ),
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ),
        'draggable'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['draggable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'clickable'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['clickable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'keyboard'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'zIndexOffset' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['zIndexOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 0,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'clr w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'ignoreForBounds' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_marker']['ignoreForBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'featureData'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['featureData'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval' => array('tl_class'  => 'clr lng',
                            'allowHtml' => true,
                            'style'     => 'min-height: 40px;',
                            'rte'       => 'ace|json'
            ),
            'sql'       => "text NULL"
        ),
    ),
);
