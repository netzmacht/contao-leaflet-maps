<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_popup'] = array
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
        )
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['layersBtn'],
                'href'                => 'table=tl_leaflet_layer',
                'icon'                => 'system/modules/leaflet/assets/img/layers.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'styles' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['styles'],
                'href'                => 'table=tl_leaflet_style',
                'icon'                => 'system/modules/leaflet/assets/img/style.png',
                'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ),
            'icons' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['icons'],
                'href'                => 'table=tl_leaflet_icon',
                'icon'                => 'system/modules/leaflet/assets/img/icons.png',
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
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\Toolkit\Dca::createToggleIconCallback(
                    'tl_leaflet_popup',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['show'],
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
            'title'  => array('title', 'alias'),
            'size'   => array('maxWidth', 'minWidth', 'maxHeight'),
            'config' => array(
                ':hide',
                'closeButton',
                'keepInView',
                'closeOnClick',
                'zoomAnimation',
                'offset',
                'className',
                'autoPan'
            ),
            'active' => array('active'),
        ),
    ),

    'metasubpalettes' => array(
        'autoPan' => array('autoPanPadding')
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
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'        => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['alias'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                \Netzmacht\Contao\Toolkit\Dca::createGenerateAliasCallback('tl_leaflet_popup', 'title'),
            ),
            'eval'          => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50', 'unique' => true),
            'sql'           => "varchar(255) NOT NULL default ''"
        ),
        'maxWidth'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['maxWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NULL"
        ),
        'minWidth'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['minWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NULL"
        ),
        'maxHeight'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['maxHeight'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => null,
            'eval'      => array('mandatory' => false, 'maxlength' => 4, 'rgxp' => 'digit', 'tl_class' => 'w50'),
            'sql'       => "int(4) NULL"
        ),
        'autoPan'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['autoPan'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50 m12', 'submitOnChange' => true),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'keepInView'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['keepInView'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => false,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'closeButton'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['closeButton'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'offset'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['offset'],
            'exclude'       => true,
            'inputType'     => 'text',
            'save_callback' => array(
                array('Netzmacht\Contao\Leaflet\Dca\Leaflet', 'validateCoordinate')
            ),
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'autoPanPadding'  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['autoPanPadding'],
            'exclude'       => true,
            'inputType'     => 'text',
            'eval'          => array(
                'maxlength'   => 255,
                'tl_class'    => 'w50',
                'nullIfEmpty' => true,
                'multiple'    => true,
                'size'        => 2,
            ),
            'sql'           => "varchar(255) NULL"
        ),
        'zoomAnimation'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['zoomAnimation'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'closeOnClick'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['closeOnClick'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => true,
            'eval'      => array('tl_class' => 'w50', 'submitOnChange' => false),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'className'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['className'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 64, 'tl_class' => 'w50'),
            'sql'       => "varchar(64) NOT NULL default ''"
        ),
        'active'                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_popup']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'filter'    => true,
            'sorting'   => true,
            'search'    => false,
            'flag'      => 12,
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
    ),
);
