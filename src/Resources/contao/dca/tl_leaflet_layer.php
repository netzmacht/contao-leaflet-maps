<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_layer'] = array
(
    'config' => array(
        'dataContainer'     => 'Table',
        'enableVersioning'  => true,
        'ctable'            => array('tl_leaflet_vector', 'tl_leaflet_marker'),
        'ondelete_callback' => array(
            \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('deleteRelations'),
        ),
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
            'mode'                    => 5,
            'fields'                  => array('title'),
            'flag'                    => 1,
            'icon'                    => 'system/modules/leaflet/assets/img/layers.png',
            'panelLayout'             => 'filter;search,limit',
            'paste_button_callback'   => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('getPasteButtons'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
            'label_callback'          => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('generateRow')
        ),
        'global_operations' => array
        (
            'styles' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['styles'],
                'href'                => 'table=tl_leaflet_style',
                'icon'                => 'system/modules/leaflet/assets/img/style.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'icons' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['icons'],
                'href'                => 'table=tl_leaflet_icon',
                'icon'                => 'system/modules/leaflet/assets/img/icons.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'popups' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['popups'],
                'href'                => 'table=tl_leaflet_popup',
                'icon'                => 'system/modules/leaflet/assets/img/popup.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            )
        ),
        'operations' => array
        (
            'markers' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['markers'],
                'href'                => 'table=tl_leaflet_marker',
                'icon'                => 'edit.gif',
                'button_callback'     => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('generateMarkersButton')
            ),
            'vectors' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['vectors'],
                'href'                => 'table=tl_leaflet_vector',
                'icon'                => 'edit.gif',
                'button_callback'     => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('generateVectorsButton'),
            ),
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::stateButton(
                    'tl_leaflet_layer',
                    'active'
                ),
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'palettes' => array(
        '__selector__' => array('type'),
    ),

    'metapalettes' => array(
        'default' => array(
            'title'    => array('title', 'alias', 'type'),
            'config'   => array(),
            'style'    => array(),
            'expert'   => array(':hide'),
            'active'   => array('active'),
        ),
        'markers extends default' => array(
            '+expert' => array('pointToLayer'),
            '+config' => array('boundsMode', 'deferred')
        ),
        'group extends default' => array(
            '+title' => array('groupType'),
            '+active' => array('boundsMode')
        ),
        'vectors extends default' => array(
            '+expert' => array('onEachFeature', 'pointToLayer'),
            '+config' => array('boundsMode', 'deferred'),
        ),
        'reference extends default' => array(
            '+title' => array('reference', 'standalone')
        ),
        'markercluster extends default' => array(
            'config' => array(
                'showCoverageOnHover',
                'zoomToBoundsOnClick',
                'removeOutsideVisibleBounds',
                'animateAddingMarkers',
                'spiderfyOnMaxZoom',
                'disableClusteringAtZoom',
                'maxClusterRadius',
                'singleMarkerMode',
            ),
            '+expert' => array(
                'polygonOptions',
                'iconCreateFunction',
                'disableDefaultStyle'
            )
        ),
        'tile extends default' => array(
            'config' => array(
                'tileUrl',
                'subdomains',
                'attribution',
                'minZoom',
                'maxZoom',
            ),
            '+expert' => array(
                'errorTileUrl',
                'tileSize',
                'tms',
                'continuousWorld',
                'noWrap',
                'zoomReverse',
                'zoomOffset',
                'maxNativeZoom',
                'opacity',
                'zIndex',
                'unloadvisibleTiles',
                'updateWhenIdle',
                'detectRetina',
                'reuseTiles',
                'bounds'
            )
        ),
        'overpass extends default' => array(
            'config' => array(
                'overpassQuery',
                'boundsMode',
                'minZoom',
                'overpassEndpoint',
                'overpassPopup'
            ),
            'style' => array(
                'amenityIcons'
            ),
            '+expert' => array(
                'onEachFeature',
                'pointToLayer',
            ),
        ),
    ),

    'metasubselectpalettes' => array(
        'type' => array(
            'provider' => array('tile_provider', 'tile_provider_variant')
        ),
        'tile_provider' => array(
            'MapBox' => array('tile_provider_key'),
            'HERE'   => array('tile_provider_key', 'tile_provider_code'),
        ),
    ),

    'metasubpalettes' => array(
        'spiderfyOnMaxZoom' => array('spiderfyDistanceMultiplier'),
        'deferred'          => array('cache'),
        'cache'             => array('cacheLifeTime')
    ),

    'fields' => array
    (
        'id'                    => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'                   => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting'               => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'tstamp'                => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'search'    => true,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'                 => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'search'        => true,
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory::aliasGenerator(
                    'tl_leaflet_layer',
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'filter'    => true,
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
                'helpwizard'         => true,
            ),
            'options'   => array_keys($GLOBALS['LEAFLET_LAYERS']),
            'reference' => &$GLOBALS['TL_LANG']['leaflet_layer'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''",
            'save_callback' => [
                \Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('clearCache'),
            ],
        ),
        'tile_provider'         => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => array_keys($GLOBALS['LEAFLET_TILE_PROVIDERS']),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'tile_provider_variant' => array(
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_variant'],
            'exclude'          => true,
            'inputType'        => 'select',
            'eval'             => array(
                'mandatory'      => false,
                'tl_class'       => 'w50',
                'submitOnChange' => true,
                'chosen'         => false,
            ),
            'options_callback' => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('getVariants'),
            'sql'              => "varchar(32) NOT NULL default ''"
        ),
        'tile_provider_key'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_key'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'clr w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'tile_provider_code'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tile_provider_code'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'deferred'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['deferred'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'groupType'                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['groupType'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'submitOnChange'     => true,
                'helpwizard'         => true,
            ),
            'default'   => 'layer',
            'options'   => array('layer', 'feature'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['groupTypes'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'reference' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['reference'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('getLayers'),
            'eval'             => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'chosen'             => true,
                'includeBlankOption' => true,
            ),
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ),
        'standalone' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['standalone'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'onEachFeature'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['onEachFeature'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|javascript',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'pointToLayer'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['pointToLayer'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|javascript',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'showCoverageOnHover' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['showCoverageOnHover'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'zoomToBoundsOnClick' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['zoomToBoundsOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'spiderfyOnMaxZoom' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['spiderfyOnMaxZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'removeOutsideVisibleBounds' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['removeOutsideVisibleBounds'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'animateAddingMarkers' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['animateAddingMarkers'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'disableClusteringAtZoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['disableClusteringAtZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('getZoomLevels'),
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
        'maxClusterRadius' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['maxClusterRadius'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'singleMarkerMode' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['singleMarkerMode'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'polygonOptions'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['polygonOptions'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|json',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'spiderfyDistanceMultiplier' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['spiderfyDistanceMultiplier'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'iconCreateFunction'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['iconCreateFunction'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|javascript',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'disableDefaultStyle' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['disableDefaultStyle'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false, 'isBoolean' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'boundsMode'          => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['boundsMode'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('getBoundsModes'),
            'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true),
            'sql'              => "varchar(6) NOT NULL default ''"
        ),
        'tileUrl' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tileUrl'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'minZoom'  => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['minZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('getZoomLevels'),
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
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['maxZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'maxNativeZoom' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['maxNativeZoom'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks::callback('getZoomLevels'),
            'eval'             => array(
                'maxlength'          => 4,
                'rgxp'               => 'digit',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'nullIfEmpty'        => true
            ),
            'sql'              => "int(4) NULL"
        ),
        'tileSize' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tileSize'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'subdomains' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['subdomains'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('maxlength' => 16, 'tl_class' => 'w50'),
            'sql'       => "varchar(16) NOT NULL default ''"
        ),
        'errorTileUrl' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['errorTileUrl'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'attribution' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['attribution'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('maxlength' => 255, 'tl_class' => 'long', 'allowHtml' => true),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'tms' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['tms'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'continuousWorld' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['continuousWorld'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'noWrap' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['noWrap'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'zoomOffset' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['zoomOffset'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'zoomReverse' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['zoomReverse'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'opacity'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['opacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '1.0',
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50 clr'),
            'sql'       => "varchar(4) NOT NULL default ''"
        ),
        'zIndex' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['zIndex'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50', 'nullIfEmpty' => true),
            'sql'       => "int(5) NULL"
        ),
        'unloadvisibleTiles' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['unloadvisibleTiles'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'updateWhenIdle' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['updateWhenIdle'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'detectRetina' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['detectRetina'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'reuseTiles' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['reuseTiles'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'bounds'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['bounds'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'multiple'=>true,
                'size'=>2,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
            ),
            'sql'           => "mediumblob NULL"
        ),
        'cache'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['cache'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'cacheLifeTime' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['cacheLifeTime'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 0,
            'eval'      => array('maxlength' => 5, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'overpassQuery'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['overpassQuery'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'overpassEndpoint' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['overpassEndpoint'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'overpassCallback'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['overpassCallback'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|javascript',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
        'minZoomIndicatorPosition' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['minZoomIndicatorPosition'],
            'exclude'   => true,
            'inputType' => 'select',
            'filter'    => true,
            'sorting'   => true,
            'options'   => array('topleft', 'topright', 'bottomleft', 'bottomright'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_layer'],
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'helpwizard' => true),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'minZoomIndicatorMessage' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['minZoomIndicatorMessage'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('tl_class' => 'clr w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'minZoomIndicatorMessageNoLayer' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['minZoomIndicatorMessageNoLayer'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'debug' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['debug'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50 m12'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'amenityIcons' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['amenityIcons'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'getIcons'),
            'eval'      => array(
                'columnFields' => array(
                    'amenity' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['amenity'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'options_callback' => \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks::callback('getAmenities'),
                        'eval'      => array(
                            'mandatory'  => true,
                            'tl_class'   => 'w50',
                            'style'      => 'width: 200px',
                            'chosen'     => true,
                        ),
                    ),
                    'icon' => array(
                        'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['amenityIcon'],
                        'exclude'   => true,
                        'inputType' => 'select',
                        'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\MarkerCallbacks', 'getIcons'),
                        'eval'      => array(
                            'mandatory'  => true,
                            'tl_class'   => 'w50',
                            'style'      => 'width: 200px',
                            'chosen'     => true,
                        ),
                    ),
                ),
            ),
            'sql'       => "blob NULL",
        ),
        'overpassPopup'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['overpassPopup'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'preserveTags'   => true,
                'decodeEntities' => true,
                'allowHtml'      => true,
                'rte'            => 'ace|javascript',
                'tl_class'       => 'clr'
            ),
            'sql'       => "mediumtext NULL"
        ),
    )
);