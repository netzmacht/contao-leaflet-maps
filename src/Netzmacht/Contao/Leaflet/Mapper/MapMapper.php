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

use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Control;
use Netzmacht\LeafletPHP\Definition\Layer;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class MapMapper maps the database map model to the leaflet definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
class MapMapper extends AbstractMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\MapModel';

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Map';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this
            ->addOptions('center', 'zoom', 'zoomControl')
            ->addOptions('dragging', 'touchZoom', 'scrollWheelZoom', 'doubleClickZoom', 'boxZoom', 'tap', 'keyboard')
            ->addOptions('trackResize', 'closeOnClick', 'bounceAtZoomLimits')
            ->addConditionalOptions('adjustZoomExtra', array('minZoom', 'maxZoom'))
            ->addConditionalOptions('keyboard', array('keyboardPanOffset', 'keyboardZoomOffset'));
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $map,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        if ($map instanceof Map && $model instanceof MapModel) {
            $this->buildCustomOptions($map, $model);
            $this->buildControls($map, $model, $mapper, $bounds);
            $this->buildLayers($map, $model, $mapper, $bounds);
            $this->buildBoundsCalculation($map, $model);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        return array(
            $this->getElementId($model, $elementId),
            $this->getElementId($model, $elementId)
        );
    }

    /**
     * Build map custom options.
     *
     * @param Map      $map   The map being built.
     * @param MapModel $model The map model.
     *
     * @return void
     */
    protected function buildCustomOptions(Map $map, MapModel $model)
    {
        if ($model->options) {
            $map->setOptions(json_decode($model->options, true));
        }
    }

    /**
     * Build map controls.
     *
     * @param Map              $map    The map being built.
     * @param MapModel         $model  The map model.
     * @param DefinitionMapper $mapper The definition mapper.
     * @param LatLngBounds     $bounds Optional bounds.
     *
     * @return void
     */
    private function buildControls(Map $map, MapModel $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $collection = ControlModel::findActiveBy('pid', $model->id, array('order' => 'sorting'));

        if (!$collection) {
            return;
        }

        foreach ($collection as $control) {
            $control = $mapper->handle($control, $bounds);

            if ($control instanceof Control) {
                $control->addTo($map);
            }
        }
    }

    /**
     * Build map layers.
     *
     * @param Map              $map    The map being built.
     * @param MapModel         $model  The map model.
     * @param DefinitionMapper $mapper Definition mapper.
     * @param LatLngBounds     $bounds Optional bounds.
     *
     * @return void
     */
    private function buildLayers(Map $map, MapModel $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $collection = $model->findActiveLayers();

        if ($collection) {
            foreach ($collection as $layer) {
                if (!$layer->active) {
                    continue;
                }

                $layer = $mapper->handle($layer, $bounds);
                if ($layer instanceof Layer) {
                    $layer->addTo($map);
                }
            }
        }
    }

    /**
     * Build map bounds calculations.
     *
     * @param Map      $map    The map being built.
     * @param MapModel $model  The map model.
     */
    private function buildBoundsCalculation(Map $map, MapModel $model)
    {
        $adjustBounds = deserialize($model->adjustBounds, true);

        if (in_array('deferred', $adjustBounds)) {
            $map->setOption('adjustBounds', true);
        }

        if (in_array('load', $adjustBounds)) {
            $map->calculateFeatureBounds();
        }
    }
}
