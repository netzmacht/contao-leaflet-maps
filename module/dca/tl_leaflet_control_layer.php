<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

$GLOBALS['TL_DCA']['tl_leaflet_control_layer'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'sql'              => array
        (
            'keys' => array
            (
                'id'      => 'primary',
                'cid,lid' => 'unique',
            )
        )
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
        'sorting'      => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'cid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'lid'          => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'mode'         => array
        (
            'sql' => "varchar(16) NOT NULL default ''"
        )
    )
);
