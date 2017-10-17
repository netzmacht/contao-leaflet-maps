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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Contao\FilesModel;
use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Group\FeatureGroup;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Plugins\Omnivore\Gpx;
use Netzmacht\LeafletPHP\Plugins\Omnivore\Kml;
use Netzmacht\LeafletPHP\Plugins\Omnivore\OmnivoreLayer;
use Netzmacht\LeafletPHP\Plugins\Omnivore\Wkt;

/**
 * Class FileLayerMapper.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class FileLayerMapper extends AbstractLayerMapper
{
    /**
     * The definition type.
     *
     * @var string
     */
    protected static $type = 'file';

    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $definitionClass = FeatureGroup::class;

    /**
     * {@inheritDoc}
     */
    public function handle(
        $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null,
        Definition $parent = null
    ) {
        $fileModel  = FilesModel::findByPk($model->file);
        $definition = $this->createInstance($model, $mapper, $request, $elementId, $fileModel);

        $this->optionsBuilder->build($definition, $model);
        $this->build($definition, $model, $mapper, $request, $parent);

        return $definition;
    }

    /**
     * {@inheritDoc}
     */
    protected function createInstance(
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null,
        FilesModel $fileModel = null
    ) {
        $layerId = $this->getElementId($model, $elementId);

        if ($fileModel instanceof FilesModel && $fileModel->type === 'file') {
            switch ($fileModel->extension) {
                case 'gpx':
                    $layer = new Gpx($layerId, $fileModel->path, []);
                    break;

                case 'kml':
                    $layer = new Kml($layerId, $fileModel->path, []);
                    break;

                case 'wkt':
                    $layer = new Wkt($layerId, $fileModel->path, []);
                    break;

                default:
                    return parent::createInstance($model, $mapper, $request, $elementId);
            }

            $customId    = $layerId . '_data';
            $customLayer = new GeoJson($customId);

            $layer->setCustomLayer($customLayer);

            return $layer;
        }

        return parent::createInstance($model, $mapper, $request, $elementId);
    }

    /**
     * {@inheritDoc}
     */
    protected function build(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        if (!$definition instanceof OmnivoreLayer) {
            return;
        }

        $customLayer = $definition->getCustomLayer();
        if ($customLayer instanceof GeoJson) {
            if ($model->boundsMode) {
                $customLayer->setOption('boundsMode', $model->boundsMode);
            }

            if ($model->pointToLayer) {
                $customLayer->setPointToLayer(new Expression($model->pointToLayer));
            }

            if ($model->onEachFeature) {
                $customLayer->setOnEachFeature(new Expression($model->onEachFeature));
            }
        }
    }
}
