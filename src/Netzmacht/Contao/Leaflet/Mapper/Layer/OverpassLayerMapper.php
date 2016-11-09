<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\OptionsBuilder;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Plugins\OverpassLayer\OverpassLayer;

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
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addOption('overpassQuery', 'query')
            ->addConditionalOption('minZoom')
            ->addConditionalOption('overpassEndpoint', 'endpoint');
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
        if (!$definition instanceof OverpassLayer) {
            return;
        }

        $minZoomIndicatorOptions        = $definition->getMinZoomIndicatorOptions();
        $minZoomIndicatorOptionsBuilder = new OptionsBuilder();
        $minZoomIndicatorOptionsBuilder
            ->addConditionalOption('minZoomIndicatorPosition', 'position')
            ->addConditionalOption('minZoomIndicatorMessageNoLayer', 'minZoomMessageNoLayer')
            ->addConditionalOption('minZoomIndicatorMessage', 'minZoomMessage');

        $minZoomIndicatorOptionsBuilder->build($minZoomIndicatorOptions, $model);

        if ($model->overpassCallback) {
            $definition->setCallback(new Expression($model->overpassCallback));
        }
    }
}
