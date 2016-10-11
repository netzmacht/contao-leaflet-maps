<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\GeoJsonMapper;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;

/**
 * Class MarkersLayerMapper maps the layer model to the markers definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class MarkersLayerMapper extends AbstractLayerMapper implements GeoJsonMapper
{
    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'markers';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, Filter $filter = null)
    {
        if ($model->deferred) {
            return 'Netzmacht\LeafletPHP\Plugins\Omnivore\GeoJson';
        }

        return 'Netzmacht\LeafletPHP\Definition\Group\GeoJson';
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
        if ($model->deferred) {
            if ($model->pointToLayer || $model->boundsMode) {
                $layer = new GeoJson($this->getElementId($model, $elementId));

                if ($model->pointToLayer) {
                    $layer->setPointToLayer(new Expression($model->pointToLayer));
                }

                if ($model->boundsMode) {
                    $layer->setOption('boundsMode', $model->boundsMode);
                }

                return array(
                    $this->getElementId($model, $elementId),
                    RequestUrl::create($model->id, null, null, $filter),
                    array(),
                    $layer
                );
            }

            return array(
                $this->getElementId($model, $elementId),
                RequestUrl::create($model->id, null, null, $filter)
            );
        }

        return parent::buildConstructArguments($model, $mapper, $filter, $elementId);
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
        if ($definition instanceof GeoJson) {
            if ($model->boundsMode) {
                $definition->setOption('boundsMode', $model->boundsMode);
            }

            $collection = $this->loadMarkerModels($model);

            if ($collection) {
                foreach ($collection as $item) {
                    $marker = $mapper->handle($item);
                    $point  = $mapper->convertToGeoJsonFeature($marker, $item);

                    if ($point) {
                        $definition->addData($point, true);
                    }
                }
            }

            if ($model->pointToLayer) {
                $definition->setPointToLayer(new Expression($model->pointToLayer));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, Filter $filter = null)
    {
        $feature    = new FeatureCollection();
        $collection = $this->loadMarkerModels($model, $filter);

        if ($collection) {
            foreach ($collection as $item) {
                $marker = $mapper->handle($item);
                $point  = $mapper->convertToGeoJsonFeature($marker, $item);

                if ($point) {
                    $feature->addFeature($point);
                }
            }
        }

        return $feature;
    }

    /**
     * Load all layer markers.
     *
     * @param \Model $model  The layer model.
     * @param Filter $filter The request filter.
     *
     * @return \Model\Collection|null
     */
    protected function loadMarkerModels(\Model $model, Filter $filter = null)
    {
        if ($model->boundsMode == 'fit') {
            return MarkerModel::findByFilter($model->id, $filter);
        }

        return MarkerModel::findByFilter($model->id);
    }
}
