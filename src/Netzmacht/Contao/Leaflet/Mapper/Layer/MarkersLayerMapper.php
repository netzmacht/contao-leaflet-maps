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

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\GeoJsonMapper;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\UI\Marker;

/**
 * Class MarkersLayerMapper maps the layer model to the markers definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class MarkersLayerMapper extends AbstractLayerMapper implements GeoJsonMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Group\GeoJson';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'markers';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        if ($model->deferred) {
            return 'Netzmacht\LeafletPHP\Plugins\Omnivore\GeoJson';
        }

        return parent::getClassName($model, $mapper, $bounds);
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
        if ($model->deferred) {

            if ($model->pointToLayer) {
                $layer = new GeoJson($this->getElementId($model, $elementId));
                $layer->setPointToLayer(new Expression($model->pointToLayer));

                return array($this->getElementId($model, $elementId), RequestUrl::create($model->id), array(), $layer);
            }

            return array($this->getElementId($model, $elementId), RequestUrl::create($model->id));
        }

        return parent::buildConstructArguments($model, $mapper, $bounds, $elementId);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        if ($definition instanceof GeoJson) {
            $collection = $this->loadMarkerModels($model);

            if ($collection) {
                foreach ($collection as $item) {
                    $marker = $mapper->handle($item);

                    if ($marker instanceof Marker) {
                        $feature = $marker->toGeoJsonFeature();
                        $feature->setProperty('affectBounds', ($item->affectBounds));

                        $definition->addData($feature, true);
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
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $feature    = new FeatureCollection();
        $collection = $this->loadMarkerModels($model);

        if ($collection) {
            foreach ($collection as $item) {
                $marker = $mapper->handle($item);

                if ($marker instanceof Marker) {
                    $feature->addFeature($marker->toGeoJsonFeature());
                }
            }
        }

        return $feature;
    }

    /**
     * Load all layer markers.
     *
     * @param \Model $model The layer model.
     *
     * @return \Model\Collection|null
     */
    protected function loadMarkerModels(\Model $model)
    {
        return MarkerModel::findActiveBy('pid', $model->id, array('order' => 'sorting'));
    }
}
