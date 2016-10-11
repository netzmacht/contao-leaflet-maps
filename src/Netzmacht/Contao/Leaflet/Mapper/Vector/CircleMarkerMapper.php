<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

/**
 * Class CircleMarkerMapper maps the database model to the circle marker definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class CircleMarkerMapper extends CircleMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\CircleMarker';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'circleMarker';
}
