<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Raster\TileLayer;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class TileLayerMapper maps the database model to the tile layer definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class TileLayerMapper extends AbstractLayerMapper
{
    /**
     * The definition class.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Raster\TileLayer';

    /**
     * The layer type.
     *
     * @var string
     */
    protected static $type = 'tile';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addConditionalOption('minZoom')
            ->addConditionalOption('maxZoom')
            ->addConditionalOption('maxNativeZoom')
            ->addConditionalOption('tileSize')
            ->addConditionalOption('subdomain')
            ->addConditionalOption('errorTileUrl')
            ->addOptions('attribution', 'tms', 'continuousWorld', 'noWrap', 'zoomReverse')
            ->addConditionalOption('zoomOffset')
            ->addConditionalOption('opacity')
            ->addOption('zIndex')
            ->addOptions('unloadvisibleTiles', 'updateWhenIdle', 'detectRetina', 'reuseTiles');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        $elementId = null
    ) {
        $arguments = parent::buildConstructArguments($model, $mapper, $filter, $elementId);

        $arguments[] = $model->tileUrl;

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $filter, $parent);

        /** @var TileLayer $definition */
        $filter = deserialize($model->bounds);

        if ($filter[0] && $filter[1]) {
            $filter = array_map(
                function ($value) {
                    return explode(',', $value, 3);
                },
                $filter
            );

            $filter = LatLngBounds::fromArray($filter);
            $definition->setBounds($filter);
        }
    }
}
