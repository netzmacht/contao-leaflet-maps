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
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\GeoJsonMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Plugins\Omnivore\GeoJson as OmnivoreGeoJson;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;

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
            if ($model->pointToLayer || $model->boundsMode) {
                $layer = new GeoJson($this->getElementId($model, $elementId));

                if ($model->pointToLayer) {
                    $layer->setPointToLayer(new Expression($model->pointToLayer));
                }

                if ($model->boundsMode) {
                    $layer->setOption('boundsMode', $model->boundsMode);
                }

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
    public function handleGeoJson(Model $model, DefinitionMapper $mapper, Request $request = null)
    {
        $feature    = new FeatureCollection();
        $collection = $this->loadMarkerModels($model, $request);

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
     * @param Model   $model   The layer model.
     * @param Request $request Optional building request.
     *
     * @return \Contao\Model\Collection|null
     */
    protected function loadMarkerModels(Model $model, Request $request = null)
    {
        $repository = $this->repositoryManager->getRepository(MarkerModel::class);

        if ($model->boundsMode == 'fit') {
            return $repository->findByFilter($model->id, $request->getFilter());
        }

        return $repository->findByFilter($model->id);
    }
}
