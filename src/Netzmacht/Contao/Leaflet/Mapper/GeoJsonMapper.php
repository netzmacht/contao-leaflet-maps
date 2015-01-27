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

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\LeafletPHP\Value\GeoJson\GeoJsonFeature;

/**
 * Interface GeoJsonMapper describes mappers which can convert their definition to a GeoJSON representation.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
interface GeoJsonMapper
{
    /**
     * Hanle the GeoJSON creation.
     *
     * @param \Model           $model  The model being mapped.
     * @param DefinitionMapper $mapper The definition mapper.
     * @param Filter           $filter Optional request filter.
     *
     * @return GeoJsonFeature|null
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, Filter $filter = null);
}
