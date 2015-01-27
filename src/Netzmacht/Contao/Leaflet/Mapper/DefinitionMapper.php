<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\Contao\Leaflet\Event\BuildDefinitionEvent;
use Netzmacht\Contao\Leaflet\Event\ConvertToGeoJsonEvent;
use Netzmacht\Contao\Leaflet\Event\GetHashEvent;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Value\GeoJson\ConvertsToGeoJsonFeature;
use Netzmacht\LeafletPHP\Value\GeoJson\Feature;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Value\GeoJson\GeoJsonFeature;
use Netzmacht\LeafletPHP\Value\LatLngBounds;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class DefinitionMapper is the main mapper instance which contains all other mappers as children.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
class DefinitionMapper
{
    /**
     * Lit of all registered mappers.
     *
     * @var Mapper[][]
     */
    private $mappers = array();

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Cache of mapped definitions.
     *
     * @var array
     */
    private $mapped = array();

    /**
     * Construct.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Add a mapper.
     *
     * @param Mapper $mapper   The mapper.
     * @param int    $priority The priority. The higher priorities get called first.
     *
     * @return $this
     */
    public function register(Mapper $mapper, $priority = 0)
    {
        $this->mappers[$priority][] = $mapper;

        krsort($this->mappers);

        return $this;
    }

    /**
     * Build a model.
     *
     * @param mixed           $model     The definition model.
     * @param Filter          $filter    Optional request filter.
     * @param string          $elementId Optional element id. If none given the mapId or alias is used.
     * @param Definition|null $parent    Optional pass the parent object.
     *
     * @return Definition|null
     *
     * @throws \RuntimeException If model could not be mapped to a definition.
     */
    public function handle($model, Filter $filter = null, $elementId = null, $parent = null)
    {
        $hash = $this->hash($model, $elementId);

        if (!isset($this->mapped[$hash])) {
            $mapper     = $this->getMapper($model);
            $definition = $mapper->handle($model, $this, $filter, $elementId, $parent);

            if ($definition) {
                $event = new BuildDefinitionEvent($definition, $model, $filter);
                $this->eventDispatcher->dispatch($event::NAME, $event);
            }

            $this->mapped[$hash] = $definition;
        }

        return $this->mapped[$hash];
    }

    /**
     * Build a model.
     *
     * @param mixed  $model  The definition model.
     * @param Filter $filter Optional request filter.
     *
     * @return FeatureCollection|Feature|null
     *
     * @throws \RuntimeException If a model could not be mapped to the GeoJSON representation.
     */
    public function handleGeoJson($model, Filter $filter = null)
    {
        $mapper = $this->getMapper($model);

        if ($mapper instanceof GeoJsonMapper) {
            return $mapper->handleGeoJson($model, $this, $filter);
        }

        throw new \RuntimeException(
            sprintf(
                'Mapper for model "%s::%s" is not a GeoJsonMapper',
                $model->getTable(),
                $model->{$model->getPk()}
            )
        );
    }

    /**
     * Convert a definition to a geo json feature.
     *
     * @param Definition $definition The leaflet definition object.
     * @param mixed      $model      The corresponding definition model.
     *
     * @return GeoJsonFeature
     * @throws \RuntimeException If a definition type is not supported.
     */
    public function convertToGeoJsonFeature(Definition $definition, $model)
    {
        if ($definition instanceof GeoJsonFeature) {
            $feature = $definition;
        } elseif ($definition instanceof ConvertsToGeoJsonFeature) {
            $feature = $definition->toGeoJsonFeature();
        } else {
            throw new \RuntimeException(
                sprintf(
                    'Definition of class "%s" could not be converted to a geo json feature.',
                    get_class($definition)
                )
            );
        }

        $event = new ConvertToGeoJsonEvent($definition, $feature, $model);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $feature;
    }

    /**
     * Get the hash of a model.
     *
     * @param mixed       $model     The definition model.
     * @param string|null $elementId Optional defined extra element id.
     *
     * @return string
     *
     * @throws \RuntimeException If no hash was created.
     */
    private function hash($model, $elementId = null)
    {
        $event = new GetHashEvent($model);
        $this->eventDispatcher->dispatch($event::NAME, $event);
        $hash = $event->getHash();

        if (!$hash) {
            throw new \RuntimeException('Could not create a hash');
        }

        if ($elementId) {
            $hash .= '.' . $elementId;
        }

        return $hash;
    }

    /**
     * Get the mapper for a definition model.
     *
     * @param mixed $model The data model.
     *
     * @return Mapper
     * @throws \RuntimeException If the mapper could not be found.
     */
    private function getMapper($model)
    {
        foreach ($this->mappers as $mappers) {
            foreach ($mappers as $mapper) {
                if ($mapper->match($model)) {
                    return $mapper;
                }
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Could not build model "%s". No matching mappers found.',
                $this->hash($model)
            )
        );
    }
}
