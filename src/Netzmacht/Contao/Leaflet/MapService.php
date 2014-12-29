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
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Definition\Map;
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
     * @param int    $mapId     The map database id.
     * @param string $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Map
     */
    public function getDefinition($mapId, $elementId = null)
    {
        $model = $this->getModel($mapId);

        return $this->mapper->handle($model, $elementId);
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
     * @param int $mapId The map id.
     * @param string $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return string
     *
     * @throws \Exception If an error occurred in the process.
     */
    public function getJavascript($mapId, $elementId = null)
    {
        $definition = $this->getDefinition($mapId, $elementId);
        $assets     = new ContaoAssets();
        $javascript = $this->leaflet->build($definition, $assets);

        $event = new GetJavascriptEvent($definition, $javascript);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getJavascript();
    }
}
