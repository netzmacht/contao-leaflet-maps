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

namespace Netzmacht\Contao\Leaflet\Mapper;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Control;
use Netzmacht\LeafletPHP\Definition\Layer;
use Netzmacht\LeafletPHP\Definition\Map;

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
    protected static $modelClass = MapModel::class;

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = Map::class;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Construct.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->optionsBuilder
            ->addOptions('center', 'zoom', 'zoomControl')
            ->addOptions('dragging', 'touchZoom', 'scrollWheelZoom', 'doubleClickZoom', 'boxZoom', 'tap', 'keyboard')
            ->addOptions('trackResize', 'closeOnClick', 'bounceAtZoomLimits')
            ->addConditionalOptions('adjustZoomExtra', ['minZoom', 'maxZoom', 'zoomSnap', 'zoomDelta'])
            ->addConditionalOptions('keyboard', ['keyboardPanOffset', 'keyboardZoomOffset']);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $map,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        if ($map instanceof Map && $model instanceof MapModel) {
            $this->buildCustomOptions($map, $model);
            $this->buildControls($map, $model, $mapper, $request);
            $this->buildLayers($map, $model, $mapper, $request);
            $this->buildBoundsCalculation($map, $model);
            $this->buildLocate($map, $model);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        return [
            $this->getElementId($model, $elementId),
            $this->getElementId($model, $elementId),
        ];
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
            $options = json_decode($model->options, true);

            if (is_array($options)) {
                $map->setOptions($options);
            }
        }

        $map->setOption('dynamicLoad', (bool) $model->dynamicLoad);
    }

    /**
     * Build map controls.
     *
     * @param Map              $map     The map being built.
     * @param MapModel         $model   The map model.
     * @param DefinitionMapper $mapper  The definition mapper.
     * @param Request          $request Optional building request.
     *
     * @return void
     */
    private function buildControls(Map $map, MapModel $model, DefinitionMapper $mapper, Request $request = null)
    {
        $repository = $this->repositoryManager->getRepository(ControlModel::class);
        $collection = $repository->findActiveBy(['pid=?'], [$model->id], ['order' => 'sorting']);

        if (!$collection) {
            return;
        }

        foreach ($collection as $control) {
            $control = $mapper->handle($control, $request, null, $map);

            if ($control instanceof Control) {
                $control->addTo($map);
            }
        }
    }

    /**
     * Build map layers.
     *
     * @param Map              $map     The map being built.
     * @param MapModel         $model   The map model.
     * @param DefinitionMapper $mapper  Definition mapper.
     * @param Request          $request Optional building request.
     *
     * @return void
     */
    private function buildLayers(Map $map, MapModel $model, DefinitionMapper $mapper, Request $request = null)
    {
        $collection = $model->findActiveLayers();

        if ($collection) {
            foreach ($collection as $layer) {
                if (!$layer->active) {
                    continue;
                }

                $layer = $mapper->handle($layer, $request, null, $map);
                if ($layer instanceof Layer) {
                    $layer->addTo($map);
                }
            }
        }
    }

    /**
     * Build map bounds calculations.
     *
     * @param Map      $map   The map being built.
     * @param MapModel $model The map model.
     *
     * @return void
     */
    private function buildBoundsCalculation(Map $map, MapModel $model)
    {
        $adjustBounds = deserialize($model->adjustBounds, true);

        if (in_array('deferred', $adjustBounds)) {
            $map->setOption('adjustBounds', true);
        }

        if ($model->boundsPadding) {
            $value = array_map('intval', explode(',', $model->boundsPadding, 4));

            if (count($value) === 4) {
                $map->setOption('boundsPaddingTopLeft', [$value[0], $value[1]]);
                $map->setOption('boundsPaddingBottomRight', [$value[2], $value[3]]);
            } elseif (count($value) === 2) {
                $map->setOption('boundsPadding', $value);
            }
        }

        if (in_array('load', $adjustBounds)) {
            $map->calculateFeatureBounds();
        }
    }

    /**
     * Build map bounds calculations.
     *
     * @param Map      $map   The map being built.
     * @param MapModel $model The map model.
     *
     * @return void
     */
    private function buildLocate(Map $map, MapModel $model)
    {
        if ($model->locate) {
            $options = [];

            $mapping = [
                'setView'            => 'locateSetView',
                'watch'              => 'locateWatch',
                'enableHighAccuracy' => 'enableHighAccuracy',
            ];

            foreach ($mapping as $option => $property) {
                if ($model->$property) {
                    $options[$option] = (bool) $model->$property;
                }
            }

            $mapping = [
                'maxZoom'    => 'locateMaxZoom',
                'timeout'    => 'locateTimeout',
                'maximumAge' => 'locateMaximumAge',
            ];

            foreach ($mapping as $option => $property) {
                if ($model->$property) {
                    $options[$option] = (int) $model->$property;
                }
            }

            $map->locate($options);
        }
    }
}
