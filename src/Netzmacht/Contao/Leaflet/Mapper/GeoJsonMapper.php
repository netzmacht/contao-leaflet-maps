<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

interface GeoJsonMapper
{
    /**
     * @param \Model           $model
     * @param DefinitionMapper $mapper
     * @param LatLngBounds     $bounds
     *
     * @return mixed
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null);
}
