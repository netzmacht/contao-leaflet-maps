<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_map'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ctable'           => array('tl_leaflet_control'),
        'sql'              => array
        (
            'keys' => array
            (
                'id'    => 'primary',
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
            'mode'                    => 1,
            'fields'                  => array('title'),
            'panelLayout'             => 'search,limit',
            'flag'                    => 1,
        ),
        'label' => array
        (
            'fields'                  => array('title', 'alias'),
            'format'                  => '%s <span class="tl_gray">[%s]</span>'
        ),
        'global_operations' => array
        (
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'controls' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['controls'],
                'href'                => 'table=tl_leaflet_control',
                'icon'                => 'system/modules/leaflet/assets/img/control.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_map']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'metapalettes' => array(
        'default' => array(
            'title'       => array('title', 'alias'),
            'zoom'        => array('center', 'zoom', 'adjustZoomExtra', 'adjustBounds', 'dynamicLoad'),
            'locate'      => array('locate'),
            'layers'      => array('layers'),
            'interaction' => array(
                'dragging',
                'touchZoom',
                'scrollWheelZoom',
                'doubleClickZoom',
                'boxZoom',
                'tap',
                'keyboard'
            ),
            'behaviour'   => array(
                'zoomControl',
                'trackResize',
                'closeOnClick',
                'bounceAtZoomLimits'
            ),
            'expert'      => array(
                'options',
            )
        ),
    ),
    'metasubpalettes' => array(
        'keyboard'        => array(
            'keyboardPanOffset',
            'keyboardZoomOffset'
        ),
        'adjustZoomExtra' => array(
            'minZoom',
            'maxZoom'
        ),
        'locate'          => array(
            ':hide',
            'locateWatch',
            'locateSetView',
            'locateMaxZoom',
            'locateTimeout',
            'locateMaximumAge',
            'enableHighAccuracy'
        ),
    ),

    'fields' => array
    (
        'id'     => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
                    'tl_leaflet_map',
                    'alias',
                    ['title'],
                    \Netzmacht\Contao\Leaflet\DependencyInjection\LeafletServices::ALIAS_GENERATOR
                ),
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateAlias')
            ),
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true),
            'sql'       => "varchar(255) NULL"
        ),
        'center'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['center'],
            'exclude'   => true,
            'inputType' => 'text',
            'save_callback' => array(
                \Netzmacht\Contao\Leaflet\Dca\Validator::callback('validateCoordinates'),
            ),
            'wizard' => array(
                array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getGeocoder')
            ),
            'eval' => array(
                'maxlength' => 255,
                'tl_class' => 'long clr',
                'nullIfEmpty' => true,
            ),
            'sql'       => "varchar(255) NULL"
        ),
        'layers' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['layers'],
            'exclude'          => true,
            'inputType'        => 'multiColumnWizard',
            'load_callback'    => array(
                \Netzmacht\Contao\Leaflet\Dca\MapCallbacks::callback('loadLayerRelations'),
            ),
            'save_callback'    => array(
                \Netzmacht\Contao\Leaflet\Dca\MapCallbacks::callback('saveLayerRelations'),
            ),
            'eval'             => array(
                'multiple'           => true,
                'doNotSaveEmpty'     => true,
                'columnFields'       => array(
                    'reference' => array
                    (
                        'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['reference'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => \Netzmacht\Contao\Leaflet\Dca\MapCallbacks::callback('getLayers'),
                        'eval'             => array(
                            'mandatory'          => true,
                            'tl_class'           => 'w50',
                            'chosen'             => true,
                            'includeBlankOption' => true,
                            'style'              => 'width: 300px'
                        ),
                        'sql'              => "int(10) unsigned NOT NULL default '0'",
                    ),
                ),
                'flatArray' => true
            ),
            'sql'              => "mediumblob NULL"
        ),
        'zoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getZoomLevels'),
            'default'          => '',
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'adjustZoomExtra'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustZoomExtra'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'minZoom'  => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['minZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'maxZoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['maxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'dragging'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['dragging'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'touchZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['touchZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'scrollWheelZoom' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['scrollWheelZoom'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('1', 'center'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
                'helpwizard'         => true,
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'][''][0]
            ),
            'sql'       => "char(6) NOT NULL default ''"
        ),
        'doubleClickZoom' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['doubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('1', 'center'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'],
            'default'   => true,
            'eval'      => array(
                'tl_class'           => 'w50',
                'helpwizard'         => true,
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomValues'][''][0]
            ),
            'sql'       => "char(6) NOT NULL default ''"
        ),
        'boxZoom'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['boxZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'tap'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['tap'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'trackResize'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['trackResize'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'bounceAtZoomLimits'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['bounceAtZoomLimits'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'closeOnClick'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['closeOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'keyboard'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboard'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'keyboardPanOffset'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardPanOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 80,
            'eval'      => array('mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'clr w50'),
            'sql'       => "int(4) NOT NULL default '80'"
        ),
        'keyboardZoomOffset'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['keyboardZoomOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 1,
            'eval'      => array('mandatory' => true, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NOT NULL default '1'"
        ),
        'zoomControl'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['zoomControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'options'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['options'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval' => array('tl_class'  => 'clr lng',
                            'allowHtml' => true,
                            'style'     => 'min-height: 40px;',
                            'rte'       => 'ace|json'
            ),
            'sql'       => "text NULL"
        ),
        'adjustBounds' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'options'   => array('load', 'deferred'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_map']['adjustBoundsOptions'],
            'eval'      => array('tl_class' => 'clr w50', 'multiple' => true, 'helpwizard' => true),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'dynamicLoad'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['dynamicLoad'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'locate'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locate'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'locateWatch'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateWatch'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'locateSetView'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateSetView'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'locateTimeout' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateTimeout'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(9) NULL"
        ),
        'locateMaximumAge' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateMaximumAge'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(9) NULL"
        ),
        'enableHighAccuracy'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_map']['enableHighAccuracy'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50 m12'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'locateMaxZoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_map']['locateMaxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks', 'getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'clr w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
    ),
);
