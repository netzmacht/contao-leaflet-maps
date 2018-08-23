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
use Contao\Model\Collection;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\GeoJsonMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\VectorModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Plugins\Omnivore\GeoJson as OmnivoreGeoJson;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;

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
    protected function getClassName(Model $model, DefinitionMapper $mapper, Request $request = null)
    {
        if ($model->deferred) {
            return OmnivoreGeoJson::class;
        }

        return GeoJson::class;
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
        if ($model->deferred) {
            $options = [];

            if ($model->pointToLayer) {
                $options['pointToLayer'] = new Expression($model->pointToLayer);
            }

            if ($model->onEachFeature) {
                $options['onEachFeature'] = new Expression($model->onEachFeature);
            }

            if ($model->boundsMode) {
                $options['boundsMode'] = $model->boundsMode;
            }

            if (!empty($options)) {
                $layer = new GeoJson($this->getElementId($model, $elementId));
                $layer->setOptions($options);

                return [
                    $this->getElementId($model, $elementId),
                    RequestUrl::create($model->id, null, null, $request),
                    [],
                    $layer,
                ];
            }

            return [
                $this->getElementId($model, $elementId),
                RequestUrl::create($model->id, null, null, $request),
            ];
        }

        return parent::buildConstructArguments($model, $mapper, $request, $elementId);
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
        if ($definition instanceof GeoJson) {
            $collection = $this->loadVectorModels($model);

            if ($model->boundsMode) {
                $definition->setOption('boundsMode', $model->boundsMode);
            }

            if ($collection) {
                foreach ($collection as $item) {
                    $vector  = $mapper->handle($item);
                    $feature = $mapper->convertToGeoJsonFeature($vector, $item);

                    if ($feature) {
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
    public function handleGeoJson(Model $model, DefinitionMapper $mapper, Request $request = null)
    {
        $definition = new FeatureCollection();
        $collection = $this->loadVectorModels($model);

        if ($collection) {
            foreach ($collection as $item) {
                $vector  = $mapper->handle($item);
                $feature = $mapper->convertToGeoJsonFeature($vector, $item);

                if ($feature) {
                    $definition->addFeature($feature, true);
                }
            }
        }

        return $definition;
    }

    /**
     * Load vector models.
     *
     * @param Model $model The layer model.
     *
     * @return Collection|null
     */
    protected function loadVectorModels(Model $model)
    {
        $repository = $this->repositoryManager->getRepository(VectorModel::class);

        return $repository->findActiveBy(['pid=?'], [$model->id], ['order' => 'sorting']);
    }

    /**
     * Add javascript callbacks.
     *
     * @param GeoJson $definition The definition.
     * @param Model   $model      The database model.
     *
     * @return void
     */
    protected function addCallbacks(GeoJson $definition, Model $model)
    {
        if ($model->pointToLayer) {
            $definition->setPointToLayer(new Expression($model->pointToLayer));
        }

        if ($model->onEachFeature) {
            $definition->setOnEachFeature(new Expression($model->onEachFeature));
        }
    }
}
