<?php

$GLOBALS['TL_DCA']['tl_leaflet_control'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'ptable'           => 'tl_leaflet_map',
        'sql'              => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('sorting'),
            'headerFields'            => array('title'),
            'flag'                    => 1,
            'child_record_callback'   => array('Netzmacht\Contao\Leaflet\Dca\Control', 'generateRow'),
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
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_leaflet_control']['toggle'],
                'icon'       => 'visible.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => \Netzmacht\Contao\DevTools\Dca::createToggleIconCallback(
                    'tl_leaflet_control',
                    'active'
                )
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_leaflet_control']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    'palettes' => array(
        '__selector__' => array('type')
    ),

    'metapalettes' => array(
        'default' => array(
            'name'     => array('title', 'alias', 'type', 'position'),
            'config'   => array(),
            'active'   => array('active')
        ),
        'zoom extends default' => array(
            'config' => array('zoomInText', 'zoomOutText', 'zoomInTitle', 'zoomOutTitle'),
        ),
        'layers extends default' => array(
            'config' => array('layers', 'collapsed', 'autoZIndex')
        ),
        'scale extends default' => array(
            'config' => array('maxWidth', 'metric', 'imperial', 'updateWhenIdle')
        ),
        'attribution extends default' => array(
            'config' => array('attributions', 'prefix')
        )
    ),

    'fields' => array
    (
        'id'     => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['title'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['alias'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'type'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['type'],
            'exclude'   => true,
            'inputType' => 'select',
            'eval'      => array(
                'mandatory'          => true,
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'chosen'             => true,
            ),
            'options'   => $GLOBALS['LEAFLET_CONTROLS'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ),
        'position'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['position'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('topleft', 'topright', 'bottomleft', 'bottomright'),
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'active'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['active'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'zoomInText'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomOutText'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomInTitle'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'zoomOutTitle'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['zoomInText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'collapsed'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['collapsed'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'autoZIndex'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['autoZIndex'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'layers'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layers'],
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => array
            (
                'tl_class'     => 'clr',
                'columnFields' => array
                (
                    'layer' => array
                    (
                        'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layer'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options_callback' => array('Netzmacht\Contao\Leaflet\Dca\Control', 'getLayers'),
                        'eval'             => array(
                            'style'  => 'width: 300px',
                            'chosen' => true,
                            'includeBlankOption' => true
                        ),
                    ),
                    'mode' => array
                    (
                        'label'            => &$GLOBALS['TL_LANG']['tl_leaflet_control']['layer'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'options'          => array('base', 'overlay'),
                        'eval'             => array(
                            'style' => 'width: 200px'
                        ),
                    ),
                )
            ),
            'sql'       => "mediumblob NULL"
        ),
        'maxWidth'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['maxWidth'],
            'exclude'   => true,
            'inputType' => 'text',
            'default'   => 100,
            'eval'      => array('tl_class' => 'w50', 'rgxp' => 'digit'),
            'sql'       => "int(5) NOT NULL default '100'"
        ),
        'metric'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['metric'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array('tl_class' => 'w50 clr'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'imperial'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['imperial'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default '1'"
        ),
        'updateWhenIdle'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['updateWhenIdle'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'prefix'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['prefix'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'attributions'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_leaflet_control']['attributions'],
            'exclude'   => true,
            'inputType' => 'listWizard',
            'eval'      => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'clr', 'allowHtml' => true),
            'sql'       => "mediumblob NULL"
        ),
    ),
);
