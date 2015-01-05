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
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Plugins\LeafletProviders\Provider;

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
     * @inheritdoc
     */
    protected function initialize()
    {
        $this
            ->addOptions('center', 'zoom', 'zoomControl')
            ->addOptions('dragging', 'touchZoom', 'scrollWheelZoom', 'doubleClickZoom', 'boxZoom', 'tap', 'keyboard')
            ->addOptions('trackResize', 'closePopupOnClick', 'bounceAtZoomLimits')
            ->addConditionalOptions('adjustZoomExtra', array('minZoom', 'maxZoom'))
            ->addConditionalOptions('keyboard', array('keyboardPanOffset', 'keyboardZoomOffset'));
    }

    /**
     * @inheritdoc
     */
    protected function doBuild(Definition $map, \Model $model, DefinitionMapper $builder)
    {
        if ($map instanceof Map && $model instanceof MapModel) {
            $this->buildCustomOptions($map, $model);
            $this->buildControls($map, $model, $builder);
            $this->buildLayers($map, $model, $builder);
        }
    }

    /**
     * @inheritdoc
     */
    protected function buildConstructArguments(\Model $model, DefinitionMapper $mapper)
    {
        return array(
            $mapper->getMapId(),
            $mapper->getMapId()
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
     */
    private function buildControls(Map $map, MapModel$model, DefinitionMapper $mapper)
    {
        $collection = ControlModel::findBy(
            array('pid=?', 'active=1'),
            array($model->id),
            array('order' => 'sorting')
        );

        if ($collection) {
            foreach ($collection as $control) {
                $control = $mapper->handle($control);
                $map->addControl($control);
            }
        }
    }

    /**
     * Build map layers.
     *
     * @param Map              $map    The map being built.
     * @param MapModel         $model  The map model.
     * @param DefinitionMapper $mapper Definition mapper.
     */
    private function buildLayers(Map $map, MapModel $model, DefinitionMapper $mapper)
    {
        $ids        = deserialize($model->layers, true);
        $collection = LayerModel::findMultipleByIds($ids);

        if ($collection) {
            foreach ($collection as $layer) {
                /** @var Provider $layer */
                $layer = $mapper->handle($layer);
                $map->addLayer($layer);
            }
        }
    }
}
