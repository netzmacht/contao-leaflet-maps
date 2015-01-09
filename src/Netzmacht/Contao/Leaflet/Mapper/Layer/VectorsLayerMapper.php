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
use Netzmacht\Contao\Leaflet\Model\VectorModel;
use Netzmacht\Contao\Leaflet\Request\RequestUrl;
use Netzmacht\Javascript\Type\Value\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Definition\Group\LayerGroup;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Plugins\Ajax\GeoJsonAjax;

class VectorsLayerMapper extends AbstractLayerMapper implements GeoJsonMapper
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
    protected static $type = 'vectors';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        if ($model->deferred) {
            return 'Netzmacht\LeafletPHP\Plugins\Ajax\GeoJsonAjax';
        }

        return parent::getClassName($model, $mapper, $bounds);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null
    ) {
        if ($definition instanceof GeoJsonAjax) {
            $definition->setUrl(RequestUrl::create($model->id));
        } elseif ($definition instanceof LayerGroup) {
            $collection = $this->loadVectorModels($model);

            if ($collection) {
                foreach ($collection as $item) {
                    $definition->addLayer($mapper->handle($item));
                }
            }
        }

        if ($definition instanceof GeoJson) {
            if ($model->pointToLayer) {
                $definition->setPointToLayer(new Expression($model->pointToLayer));
            }

            if ($model->onEachFeature) {
                $definition->setOnEachFeature(new Expression($model->onEachFeature));
            }
        }
    }

    /**
     * @param \Model           $model
     * @param DefinitionMapper $mapper
     * @param LatLngBounds     $bounds
     *
     * @return mixed
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $feature    = new FeatureCollection();
        $collection = $this->loadVectorModels($model);

        if ($collection) {
            foreach ($collection as $item) {
                $feature->addFeature($mapper->handle($item)->toGeoJson());
            }
        }

        return $feature;
    }

    /**
     * @param \Model $model
     *
     * @return \Model\Collection|null
     */
    protected function loadVectorModels(\Model $model)
    {
        $collection = VectorModel::findBy(
            array('active=1', 'pid=?'),
            array($model->id),
            array('order' => 'sorting')
        );

        return $collection;
    }
}

