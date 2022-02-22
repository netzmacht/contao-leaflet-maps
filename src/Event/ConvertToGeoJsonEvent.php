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

namespace Netzmacht\Contao\Leaflet\Event;

use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Value\GeoJson\GeoJsonObject;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ConvertToGeoJsonEvent is emitted when the DefinitionMapper converts a definition to a geo json feature.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class ConvertToGeoJsonEvent extends Event
{
    const NAME = 'netzmacht.contao_leaflet.mapper.convert_to_geojson';

    /**
     * The definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * The GeoJSON object.
     *
     * @var GeoJsonObject
     */
    private $geoJson;

    /**
     * The definition model.
     *
     * @var mixed
     */
    private $model;

    /**
     * Construct.
     *
     * @param Definition    $definition The definition.
     * @param GeoJsonObject $geoJson    The GeoJSON object.
     * @param mixed         $model      The corresponding model. Usually a \Model but could be everything.
     */
    public function __construct(Definition $definition, GeoJsonObject $geoJson, $model)
    {
        $this->definition = $definition;
        $this->geoJson    = $geoJson;
        $this->model      = $model;
    }

    /**
     * Get the definition.
     *
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Get the geoJson representation.
     *
     * @return GeoJsonObject
     */
    public function getGeoJson()
    {
        return $this->geoJson;
    }

    /**
     * Get the model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
