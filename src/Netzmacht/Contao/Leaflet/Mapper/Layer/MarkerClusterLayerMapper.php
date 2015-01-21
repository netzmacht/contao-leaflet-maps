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
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\JavascriptBuilder\Type\AnonymousFunction;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\HasEvents;
use Netzmacht\LeafletPHP\Definition\Layer;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Plugins\MarkerCluster\MarkerClusterGroup;
use Netzmacht\LeafletPHP\Plugins\Omnivore\OmnivoreLayer;

/**
 * Class MarkerClusterLayerMapper maps the layer database model to the marker cluster definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class MarkerClusterLayerMapper extends AbstractLayerMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Plugins\MarkerCluster\MarkerClusterGroup';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'markercluster';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this
            ->addOptions('showCoverageOnHover', 'zoomToBoundsOnClick', 'spiderfyOnMaxZoom')
            ->addOption('removeOutsideVisibleBounds')
            ->addConditionalOption('maxClusterRadius')
            ->addConditionalOption('singleMarkerMode')
            ->addConditionalOption('animateAddingMarkers')
            ->addConditionalOption('disableClusteringAtZoom')
            ->addConditionalOption('spiderfyDistanceMultiplier');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $bounds, $parent);

        /** @var MarkerClusterGroup $definition */

        if ($model->iconCreateFunction) {
            $definition->setIconCreateFunction(new Expression($model->iconCreateFunction));
        }

        if ($model->polygonOptions) {
            $definition->setPolygonOptions((array) json_decode($model->polygonOptions, true));
        }

        if (!$model->disableDefaultStyle) {
            $GLOBALS['TL_CSS'][] = 'assets/leaflet/libs/leaflet-markercluster/MarkerCluster.Default.css||static';
        }

        $definition->setOption('affectBounds', (bool) $model->affectBounds);

        $collection = LayerModel::findBy(
            array('pid=?', 'active=1'),
            array($model->id),
            array('order' => 'sorting')
        );

        if ($collection) {
            foreach ($collection as $layerModel) {
                $layer = $mapper->handle($layerModel);

                if ($layer instanceof Layer) {
                    $definition->addLayer($layer);

                    if ($layer instanceof OmnivoreLayer) {
                        $callback = new AnonymousFunction();
                        $callback->addLine('layers.' . $definition->getId() . '.addLayers(this.getLayers())');

                        $layer->on('ready', $callback);
                    }
                }
            }
        }
    }
}
