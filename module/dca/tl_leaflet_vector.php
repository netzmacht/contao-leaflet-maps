<?php

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
                'id' => 'primary'
            )
        )
    ),

    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('sorting'),
            'flag'                    => 1,
            'headerFields'            => array('title', 'type'),
            'child_record_callback'   => array('Netzmacht\Contao\Leaflet\Dca\Vector', 'generateRow'),
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
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
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\DevTools\Dca::createToggleIconCallback(
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
            'style'  => array(
                ':hide',
                'stroke',
                'color',
                'weight',
                'opacity',
                'dashArray',
                'fill',
                'lineCap',
                'lineJoin',
            ),
            'popup'  => array(':hide','addPopup'),
            'config' => array(':hide', 'clickable', 'className'),
            'active' => array('active')
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

        'rectangle extends polygon' => array(),

        'circle extends default' => array(
            '+data' => array('coordinates', 'radius'),
        ),

        'circleMarker extends circle' => array(),
    ),
    'metasubpalettes' => array(
        'addPopup'   => array('popupContent'),
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
        'pid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting'               => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'type'                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_layer']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => &$GLOBALS['LEAFLET_VECTORS'],
            'reference' => &$GLOBALS['TL_LANG']['leaflet_layer'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'addPopup'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['addPopup'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
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
        'stroke'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['stroke'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50 m12'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'color'                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['color'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => array(
                \Netzmacht\Contao\DevTools\Dca::createColorPickerCallback(),
            ),
            'eval'      => array(
                'tl_class'       => 'w50 wizard',
                'maxlength'      => 7,
                'decodeEntities' => true
            ),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'weight'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['weight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 5,
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'clr w50'),
            'sql'       => "int(4) NOT NULL default '5'"
        ),
        'opacity'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['opacity'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.5',
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "varchar(4) NOT NULL default '0.5'"
        ),
        'fill'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['fill'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'fillColor'                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['fillColor'],
            'exclude'   => true,
            'inputType' => 'text',
            'wizard'    => array(
                \Netzmacht\Contao\DevTools\Dca::createColorPickerCallback(),
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['weight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => '0.2',
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "varchar(4) NOT NULL default '0.2'"
        ),
        'dashArray'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['dashArray'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 32, 'tl_class' => 'w50 clr'),
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'lineCap'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['lineCap'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('butt', 'round', 'square', 'inherit'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['lineCaps'],
            'eval'      => array('mandatory' => false, 'tl_class' => 'w50 clr', 'includeBlankOption' => true, 'helpwizard'),
            'sql'       => "varchar(8) NOT NULL default ''"
        ),
        'lineJoin'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['lineJoin'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('miter', 'round', 'bevel', 'inherit'),
            'reference' => &$GLOBALS['TL_LANG']['tl_leaflet_vector']['lineJoins'],
            'eval'      => array('mandatory' => false, 'tl_class' => 'w50', 'includeBlankOption' => true, 'helpwizard'),
            'sql'       => "varchar(8) NOT NULL default ''"
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
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'wizard'        => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'getGeocoder')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'long clr',
                'nullIfEmpty' => true,
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
            'sql'       => "longblob NULL"
        )
    ),
);
