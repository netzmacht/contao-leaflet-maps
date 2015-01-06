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
     * Registered builders.
     *
     * @var AbstractMapper[][]
     */
    private $builders = array();

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Map id of the current built map.
     *
     * @var string
     */
    private $mapId;

    /**
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

        ksort($this->builders);

        return $this;
    }

    /**
     * Get the map id of the current built map.
     *
     * @return string
     */
    public function getMapId()
    {
        return $this->mapId;
    }

    /**
     * Build a model.
     *
     * @param \Model       $model     The definition model.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Definition
     */
    public function handle(\Model $model, LatLngBounds $bounds = null, $elementId = null)
    {
        $hash = $model->getTable() . '.' . $model->{$model->getPk()};

        if (isset($this->mapped[$hash])) {
            return $this->mapped[$hash];
        }

        $this->mapId = $elementId ?: ($model->alias ?: ('map_' . $model->id));

        foreach ($this->builders as $builders) {
            foreach($builders as $builder) {
                if ($builder->match($model)) {
                    $definition = $builder->handle($model, $this, $bounds);

                    $event = new BuildDefinitionEvent($definition, $model, $bounds);
                    $this->eventDispatcher->dispatch($event::NAME, $event);

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
     * @param \Model       $model     The definition model.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     *
     * @return FeatureCollection|Feature
     */
    public function handleGeoJson(\Model $model, LatLngBounds $bounds = null)
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
}
