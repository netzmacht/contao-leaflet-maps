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
 * Class PolygonMapper maps the database model to the polygon definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class PolygonMapper extends PolylineMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\Polygon';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'polygon';
}
