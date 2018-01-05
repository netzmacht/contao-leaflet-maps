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

namespace Netzmacht\Contao\Leaflet\Mapper;

use Contao\Model;
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
     * @param Model            $model   The model being mapped.
     * @param DefinitionMapper $mapper  The definition mapper.
     * @param Request          $request Optional building request.
     *
     * @return GeoJsonFeature|null
     */
    public function handleGeoJson(Model $model, DefinitionMapper $mapper, Request $request = null);
}
