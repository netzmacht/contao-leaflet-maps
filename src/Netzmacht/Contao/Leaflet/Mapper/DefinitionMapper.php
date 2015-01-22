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
use Netzmacht\Contao\Leaflet\Event\GetHashEvent;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\GeoJson\Feature;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
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
     * @param Mapper $mapper  The mapper.
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
     * @param LatLngBounds    $bounds    Optional bounds where elements should be in.
     * @param string          $elementId Optional element id. If none given the mapId or alias is used.
     * @param Definition|null $parent    Optional pass the parent object.
     *
     * @return Definition|null
     *
     * @throws \RuntimeException If model could not be mapped to a definition.
     */
    public function handle($model, LatLngBounds $bounds = null, $elementId = null, $parent = null)
    {
        $hash = $this->hash($model, $elementId);

        if (!isset($this->mapped[$hash])) {
            $mapper     = $this->getMapper($model);
            $definition = $mapper->handle($model, $this, $bounds, $elementId, $parent);

            if ($definition) {
                $event = new BuildDefinitionEvent($definition, $model, $bounds);
                $this->eventDispatcher->dispatch($event::NAME, $event);
            }

            $this->mapped[$hash] = $definition;
        }

        return $this->mapped[$hash];
    }

    /**
     * Build a model.
     *
     * @param mixed        $model  The definition model.
     * @param LatLngBounds $bounds Optional bounds where elements should be in.
     *
     * @return FeatureCollection|Feature|null
     *
     * @throws \RuntimeException If a model could not be mapped to the GeoJSON representation.
     */
    public function handleGeoJson($model, LatLngBounds $bounds = null)
    {
        $mapper = $this->getMapper($model);

        if ($mapper instanceof GeoJsonMapper) {
            return $mapper->handleGeoJson($model, $this, $bounds);
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
                'Could not build model "". No matching mappers found.',
                $this->hash($model)
            )
        );
    }
}
