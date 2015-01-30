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
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Frontend\DataController;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
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
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * The request input.
     *
     * @var \Input
     */
    private $input;

    /**
     * Construct.
     *
     * @param DefinitionMapper $mapper          The definition mapper.
     * @param Leaflet          $leaflet         The Leaflet instance.
     * @param EventDispatcher  $eventDispatcher The Contao event dispatcher.
     * @param \Input           $input           Thw request input.
     */
    public function __construct(
        DefinitionMapper $mapper,
        Leaflet $leaflet,
        EventDispatcher $eventDispatcher,
        \Input $input
    ) {
        $this->mapper          = $mapper;
        $this->leaflet         = $leaflet;
        $this->eventDispatcher = $eventDispatcher;
        $this->input           = $input;
    }

    /**
     * Get map definition.
     *
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param Filter       $filter    Optional request filter.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Map
     */
    public function getDefinition($mapId, Filter $filter = null, $elementId = null)
    {
        if ($mapId instanceof MapModel) {
            $model = $mapId;
            $mapId = $model->id;
        } else {
            $model = $this->getModel($mapId);
        }

        RequestUrl::setFor($elementId ?: $mapId);
        $definition = $this->mapper->reset()->handle($model, $filter, $elementId);
        RequestUrl::setFor(null);

        return $definition;
    }

    /**
     * Get map model.
     *
     * @param int|string $mapId Model id or alias.
     *
     * @return MapModel
     *
     * @throws \InvalidArgumentException If no model is found.
     */
    public function getModel($mapId)
    {
        $model = MapModel::findByIdOrAlias($mapId);

        if ($model === null) {
            throw new \InvalidArgumentException(sprintf('Model "%s" not found', $mapId));
        }

        return $model;
    }

    /**
     * Get map javascript.
     *
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param Filter       $filter    Optional request filter.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     * @param string       $template  The template being used for generating.
     * @param string       $style     Optional style attributes.
     *
     * @return string
     * @throws \Exception If generating went wrong.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function generate(
        $mapId,
        Filter $filter = null,
        $elementId = null,
        $template = 'leaflet_map_js',
        $style = ''
    ) {
        $definition = $this->getDefinition($mapId, $filter, $elementId);
        $assets     = new ContaoAssets();
        $template   = \Controller::getTemplate($template);

        // @codingStandardsIgnoreStart - Set for the template.
        $javascript = $this->leaflet->build($definition, $assets);
        $mapId      = $definition->getId();
        // @codingStandardsIgnoreEnd

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
     * @param Filter|null    $filter  Filter data.
     *
     * @return FeatureCollection
     *
     * @throws \InvalidArgumentException If a layer could not be found.
     */
    public function getFeatureCollection($layerId, Filter $filter = null)
    {
        if ($layerId instanceof LayerModel) {
            $model = $layerId;
        } else {
            $model = LayerModel::findByPK($layerId);
        }

        if (!$model || !$model->active) {
            throw new \InvalidArgumentException(sprintf('Could not find layer "%s"', $layerId));
        }

        return $this->mapper->handleGeoJson($model, $filter);
    }

    /**
     * Handle ajax request.
     *
     * @param string $identifier The request identifier.
     * @param bool   $exit       Exit if ajax request is detected.
     *
     * @return void
     * @throws \RuntimeException IF the input data does not match.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function handleAjaxRequest($identifier, $exit = true)
    {
        $input = $this->input->get('leaflet', true);

        // Handle ajax request.
        if ($input) {
            $data   = explode(',', base64_decode($input));
            $data[] = $this->input->get('f');
            $data[] = $this->input->get('v');

            if (count($data) != 6) {
                throw new \RuntimeException('Bad request. Could not resolve query params');
            }

            $data = array_combine(array('for', 'type', 'id', 'format', 'filter', 'values'), $data);
            $data = array_filter($data);

            if (empty($data['for']) || $data['for'] != $identifier) {
                return;
            }

            $controller = new DataController($this, $data);
            $controller->execute();

            if ($exit) {
                exit;
            }
        }
    }
}
