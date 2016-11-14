<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Model;
use Netzmacht\Contao\Leaflet\Definition\Layer\OverpassLayer;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;

/**
 * Class OverpassLayerMapper
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class OverpassLayerMapper extends AbstractLayerMapper
{
    /**
     * The definition type.
     *
     * @var string
     */
    protected static $type = 'overpass';

    /**
     * The definition class.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\Contao\Leaflet\Definition\Layer\OverpassLayer';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addOption('query', 'overpassQuery')
            ->addOption('minZoom')
            ->addOption('boundsMode')
            ->addOption('overpassEndpoint', 'endpoint');
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        Definition $parent = null
    ) {
        if (!$definition instanceof OverpassLayer) {
            return;
        }

        $amenityIconsMap = $this->buildAmenityIconsMap($model);
        $definition->setOption('amenityIcons', $amenityIconsMap);

        if ($model->pointToLayer) {
            $definition->setPointToLayer(new Expression($model->pointToLayer));
        }

        if ($model->onEachFeature) {
            $definition->setOnEachFeature(new Expression($model->onEachFeature));
        }

        if ($model->overpassPopup) {
            $definition->setOption('overpassPopup', new Expression($model->overpassPopup));
        }
    }

    /**
     * Build the amenity icons map.
     *
     * @param Model $model Definition model.
     *
     * @return array
     */
    protected function buildAmenityIconsMap(Model $model)
    {
        $amenityIconsConfig = deserialize($model->amenityIcons, true);
        $amenityIconsMap    = [];
        foreach ($amenityIconsConfig as $config) {
            if (!$config['amenity'] || !$config['icon']) {
                continue;
            }

            $amenityIconsMap[$config['amenity']] = $config['icon'];
        }

        if ($amenityIconsMap) {
            $collection    = IconModel::findMultipleByIds(array_unique($amenityIconsMap));
            $icons         = [];

            if ($collection) {
                foreach ($collection as $iconModel) {
                    $icons[$iconModel->id] = $iconModel->alias ?: $iconModel->id;
                }

                foreach ($amenityIconsMap as $amenity => $iconId) {
                    if (isset($icons[$iconId])) {
                        $amenityIconsMap[$amenity] = $icons[$iconId];
                    }
                }
            }
        }

        return $amenityIconsMap;
    }
}
