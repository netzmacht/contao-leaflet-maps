<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_vector'] = array
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
        )
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
            'child_record_callback'   => Netzmacht\Contao\Leaflet\Dca\VectorCallbacks::callback('generateRow'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
        ),
        'global_operations' => array
        (
            'styles' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['styles'],
                'href'                => 'table=tl_leaflet_style',
                'icon'                => 'system/modules/leaflet/assets/img/style.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'popups' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popups'],
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::stateButton(
                    'tl_leaflet_vector',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['show'],
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
            'data'   => array(),
            'popup'  => array(':hide','addPopup'),
            'config' => array(':hide', 'style', 'className', 'clickable'),
            'expert' => array(':hide', 'featureData'),
            'active' => array('active', 'ignoreForBounds')
        ),

        'polyline extends default' => array(
            '+config' => array('smoothFactor', 'noClip'),
            'data'    => array('data')
        ),

        'multiPolyline extends polyline' => array(
            'data' => array('multiData')
        ),

        'polygon extends polyline' => array(),

        'multiPolygon extends multiPolyline' => array(
        ),

        'rectangle extends polygon' => array(
            'data' => array('bounds'),
        ),

        'circle extends default' => array(
            '+data' => array('coordinates', 'radius'),
        ),

        'circleMarker extends circle' => array(),
    ),
    'metasubpalettes' => array(
        'addPopup'  => array('popup', 'popupContent'),
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
        'pid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting'               => array
        (
            'sql'     => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
        ),
        'title'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'filter'    => false,
            'sorting'   => true,
            'search'    => true,
            'flag'      => 1,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
                    'tl_leaflet_vector',
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'filter'    => true,
            'sorting'   => true,
            'search'    => false,
            'flag'      => 1,
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ),
            'options'   => &$GLOBALS['LEAFLET_VECTORS'],
            'reference' => &$GLOBALS['TL_LANG']['leaflet_vector'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'sorting'   => true,
            'search'    => false,
            'flag'      => 12,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'addPopup'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'popup'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popup'],
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
            'label'       => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['popupContent'],
            'exclude'     => true,
            'inputType'   => 'text',
            'eval'        => array('mandatory' => true, 'rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'),
            'explanation' => 'insertTags',
            'sql'         => "mediumtext NULL"
        ),
        'style'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['style'],
            'exclude'   => true,
            'inputType' => 'select',
            'options_callback' => Netzmacht\Contao\Leaflet\Dca\VectorCallbacks::callback('getStyles'),
            'eval'      => array(
                'mandatory'  => false,
                'tl_class'   => 'w50',
                'chosen'     => true,
                'includeBlankOption' => true,
            ),
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ),
        'clickable'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['clickable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'm12 w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'className'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 64, 'tl_class' => 'w50'),
            'sql'       => "varchar(64) NOT NULL default ''"
        ),
        'coordinates'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['coordinates'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateCoordinates')
            ),
            'wizard'        => array(
                array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getGeocoder')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
                'mandatory'   => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'radius'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['radius'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 5,
            'eval'      => array('mandatory' => false, 'maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'clr w50'),
            'sql'       => "int(10) NOT NULL default '5'"
        ),
        'data' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'],
            'inputType' => 'textarea',
            'search'    => false,
            'eval'      => array('mandatory' => true, 'alwaysSave' => true),
            'save_callback' => array(
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateMultipleCoordinates')
            ),
            'sql'       => "longblob NULL"
        ),
        'multiData' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['multiData'],
            'inputType' => 'multiColumnWizard',
            'search'    => false,
            'eval'      => array(
                'mandatory'    => true,
                'alwaysSave'   => true,
                'flatArray'    => true,
                'columnFields' => array
                (
                    'data' => array
                    (
                        'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['data'],
                        'inputType' => 'textarea',
                        'search'    => false,
                        'eval'      => array('alwaysSave' => true, 'style' => 'width:600px'),
                    )
                )
            ),
            'save_callback' => array(
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateMultipleCoordinateSets')
            ),
            'sql'       => "longblob NULL"
        ),
        'bounds'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['bounds'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(),
            'eval'          => array(
                'maxlength'   => 255,
                'multiple'=>true,
                'size'=>2,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
            ),
            'sql'           => "mediumblob NULL"
        ),
        'ignoreForBounds' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['ignoreForBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'featureData'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['featureData'],
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
