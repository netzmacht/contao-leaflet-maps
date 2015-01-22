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
use Netzmacht\Contao\Leaflet\Model\VectorModel;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\GeoJson\ConvertsToGeoJsonFeature;
use Netzmacht\LeafletPHP\Definition\GeoJson\Feature;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\GeoJson\GeoJsonFeature;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Definition\Group\LayerGroup;
use Netzmacht\LeafletPHP\Definition\Layer;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\Vector;

/**
 * Class VectorsLayerMapper maps the layer model for the Vectors layer definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class VectorsLayerMapper extends AbstractLayerMapper implements GeoJsonMapper
{
    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'vectors';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
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
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        if ($model->deferred) {
            $options = array();

            if ($model->pointToLayer) {
                $options['pointToLayer'] = new Expression($model->pointToLayer);
            }

            if ($model->onEachFeature) {
                $options['onEachFeature'] = new Expression($model->onEachFeature);
            }

            if ($model->affectBounds) {
                $options['affectBounds'] = (bool) $model->affectBounds;
            }

            if (!empty($options)) {
                $layer = new GeoJson($this->getElementId($model, $elementId));
                $layer->setOptions($options);

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
            $collection = $this->loadVectorModels($model);

            if ($model->affectBounds) {
                $definition->setOption('affectBounds', true);
            }

            if ($collection) {
                foreach ($collection as $item) {
                    $vector = $mapper->handle($item);

                    if ($vector instanceof ConvertsToGeoJsonFeature) {
                        $feature = $vector->toGeoJsonFeature();

                        if ($feature instanceof Feature && ($item->ignoreForBounds || !$model->affectBounds)) {
                            $feature->setProperty('ignoreForBounds', true);
                        }

                        $definition->addData($feature, true);
                    }
                }
            }

            $this->addCallbacks($definition, $model);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $feature    = new FeatureCollection();
        $collection = $this->loadVectorModels($model);

        if ($collection) {
            foreach ($collection as $item) {
                $vector = $mapper->handle($item);

                if ($vector instanceof ConvertsToGeoJsonFeature) {
                    $vector = $vector->toGeoJsonFeature();
                }

                if ($vector instanceof GeoJsonFeature) {
                    if ($vector instanceof Feature && ($item->ignoreForBounds || !$model->affectBounds)) {
                        $vector->setProperty('ignoreForBounds', true);
                    }

                    $feature->addFeature($vector);
                }
            }
        }

        return $feature;
    }

    /**
     * Load vector models.
     *
     * @param \Model $model The layer model.
     *
     * @return \Model\Collection|null
     */
    protected function loadVectorModels(\Model $model)
    {
        return VectorModel::findActiveBy('pid', $model->id, array('order' => 'sorting'));
    }

    /**
     * Add javascript callbacks.
     *
     * @param GeoJson $definition The definition.
     * @param \Model  $model      The database model.
     *
     * @return void
     */
    protected function addCallbacks(GeoJson $definition, \Model $model)
    {
        if ($model->pointToLayer) {
            $definition->setPointToLayer(new Expression($model->pointToLayer));
        }

        if ($model->onEachFeature) {
            $definition->setOnEachFeature(new Expression($model->onEachFeature));
        }
    }
}
