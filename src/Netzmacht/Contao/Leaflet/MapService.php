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
     * The event dispatcher.
     *
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
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Map
     */
    public function getDefinition($mapId, LatLngBounds $bounds = null, $elementId = null)
    {
        if ($mapId instanceof MapModel) {
            $model = $mapId;
        } else {
            $model = $this->getModel($mapId);
        }

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
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param LatLngBounds $bounds    Optional bounds where elements should be in.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return string
     */
    public function getJavascript($mapId, LatLngBounds $bounds = null, $elementId = null, $template = 'leaflet_map_js')
    {
        $definition = $this->getDefinition($mapId, $bounds, $elementId);
        $assets     = new ContaoAssets();

        $template   = \Controller::getTemplate($template);
        $javascript = $this->leaflet->build($definition, $assets);
        $mapId      = $definition->getId();

        ob_start();
        include $template;
        $content = ob_get_contents();
        ob_end_clean();

        $event = new GetJavascriptEvent($definition, $content);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getJavascript();
    }

    /**
     * Get feature collection of a layer.
     *
     * @param LayerModel|int $layerId The layer database id or layer model.
     * @param LatLngBounds   $bounds  Filter features in the bounds.
     *
     * @return FeatureCollection
     *
     * @throws \InvalidArgumentException If a layer could not be found.
     */
    public function getFeatureCollection($layerId, LatLngBounds $bounds = null)
    {
        if ($layerId instanceof LayerModel) {
            $model = $layerId;
        } else {
            $model = LayerModel::findByPK($layerId);
        }

        if (!$model || !$model->active) {
            throw new \InvalidArgumentException(sprintf('Could not find layer "%s"', $layerId));
        }

        return $this->mapper->handleGeoJson($model, $bounds);
    }
}
