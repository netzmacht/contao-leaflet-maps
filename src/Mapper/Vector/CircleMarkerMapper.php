<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

use Netzmacht\LeafletPHP\Definition\Vector\CircleMarker;

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
    protected static $definitionClass = CircleMarker::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'circleMarker';
}
