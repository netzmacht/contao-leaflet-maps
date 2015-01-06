<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet;

use Netzmacht\Contao\Leaflet\Event\GetJavascriptEvent;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollectionAggregate;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class MapService.
 *
 * @package Netzmacht\Contao\Leaflet
 */
class MapService
{
    /**
     * The definition mapper.
     *
     * @var DefinitionMapper
     */
    private $mapper;

    /**
     * The leaflet service.
     *
     * @var Leaflet
     */
    private $leaflet;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param DefinitionMapper $mapper          The definition mapper.
     * @param Leaflet          $leaflet         The Leaflet instance.
     * @param EventDispatcher  $eventDispatcher The Contao event dispatcher.
     */
    public function __construct(DefinitionMapper $mapper, Leaflet $leaflet, EventDispatcher $eventDispatcher)
    {
        $this->mapper          = $mapper;
        $this->leaflet         = $leaflet;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get map definition.
     *
     * @param int          $mapId     The map database id.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Map
     */
    public function getDefinition($mapId, LatLngBounds $bounds = null, $elementId = null)
    {
        $model = $this->getModel($mapId);

        return $this->mapper->handle($model, $bounds, $elementId);
    }

    /**
     * Get map model.
     *
     * @param int $mapId Model id.
     *
     * @return MapModel
     *
     * @throws \InvalidArgumentException If no model is found.
     */
    public function getModel($mapId)
    {
        $model = MapModel::findByPk($mapId);

        if ($model === null) {
            throw new \InvalidArgumentException(sprintf('Model "%s" not found', $mapId));
        }

        return $model;
    }

    /**
     * Get map javascript.
     *
     * @param int          $mapId     The map id.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return string
     */
    public function getJavascript($mapId, LatLngBounds $bounds = null, $elementId = null)
    {
        $definition = $this->getDefinition($mapId, $bounds, $elementId);
        $assets     = new ContaoAssets();
        $javascript = $this->leaflet->build($definition, $assets);

        $event = new GetJavascriptEvent($definition, $javascript);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getJavascript();
    }

    /**
     * Get feature collection of a layer.
     *
     * @param int          $layerId The layer id.
     * @param LatLngBounds $bounds  Filter features in the bounds.
     *
     * @return FeatureCollection
     */
    public function getFeatureCollection($layerId, LatLngBounds $bounds = null)
    {
        $model = LayerModel::findByPK($layerId);

        if (!$model || !$model->active) {
            throw new \InvalidArgumentException(sprintf('Could not find layer "%s"', $layerId));
        }

        return $this->mapper->handleGeoJson($model, $bounds);
    }
}
