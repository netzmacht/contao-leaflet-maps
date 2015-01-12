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
 * Class DefinitionBuilder is the main builder instance which contains all other builders as children.
 *
 * @package Netzmacht\Contao\Leaflet\Builder
 */
class DefinitionMapper
{
    /**
     * Lit of all registered mappers.
     *
     * @var Mapper[][]
     */
    private $builders = array();

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
     * Add a builder.
     *
     * @param Mapper $builder  The builder.
     * @param int    $priority The priority. The higher priorities get called first.
     *
     * @return $this
     */
    public function register(Mapper $builder, $priority = 0)
    {
        $this->builders[$priority][] = $builder;

        krsort($this->builders);

        return $this;
    }

    /**
     * Build a model.
     *
     * @param mixed        $model     The definition model.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Definition|null
     *
     * @throws \RuntimeException If model could not be mapped to a definition.
     */
    public function handle($model, LatLngBounds $bounds = null, $elementId = null)
    {
        $hash = $this->getHash($model, $elementId);

        if (isset($this->mapped[$hash])) {
            return $this->mapped[$hash];
        }

        foreach ($this->builders as $builders) {
            foreach ($builders as $builder) {
                if ($builder->match($model)) {
                    $definition = $builder->handle($model, $this, $bounds, $elementId);

                    if ($definition) {
                        $event = new BuildDefinitionEvent($definition, $model, $bounds);
                        $this->eventDispatcher->dispatch($event::NAME, $event);
                    }

                    $this->mapped[$hash] = $definition;

                    return $definition;
                }
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Could not build model "%s::%s". No matching builders found.',
                $model->getTable(),
                $model->{$model->getPk()}
            )
        );
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
        foreach ($this->builders as $builders) {
            foreach ($builders as $builder) {
                if (!$builder->match($model)) {
                    continue;
                }

                if ($builder instanceof GeoJsonMapper) {
                    return $builder->handleGeoJson($model, $this, $bounds);
                }

                throw new \RuntimeException(
                    sprintf(
                        'Builder for model "%s::%s" is not a GeoJsonMapper',
                        $model->getTable(),
                        $model->{$model->getPk()}
                    )
                );
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Could not build geo json of model "%s::%s". No matching builders found.',
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
    protected function getHash($model, $elementId)
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
}
