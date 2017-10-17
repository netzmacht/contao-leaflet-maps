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

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Contao\Model;
use Contao\StringUtil;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Raster\TileLayer;
use Netzmacht\LeafletPHP\Value\LatLngBounds;

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
    protected static $definitionClass = TileLayer::class;

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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        $arguments = parent::buildConstructArguments($model, $mapper, $request, $elementId);

        $arguments[] = $model->tileUrl;

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $request, $parent);

        /** @var TileLayer $definition */
        $bounds = StringUtil::deserialize($model->bounds);

        if ($request[0] && $request[1]) {
            $bounds = array_map(
                function ($value) {
                    return explode(',', $value, 3);
                },
                $bounds
            );

            $bounds = LatLngBounds::fromArray($bounds);
            $definition->setBounds($bounds);
        }
    }
}
