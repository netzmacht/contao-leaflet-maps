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
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Group\LayerGroup;
use Netzmacht\LeafletPHP\Definition\Layer;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class GroupLayerMapper maps the layer model to the group layer definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class GroupLayerMapper extends AbstractLayerMapper
{
    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'group';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        if ($model->groupType === 'feature') {
            return 'Netzmacht\LeafletPHP\Definition\Group\FeatureGroup';
        }

        return 'Netzmacht\LeafletPHP\Definition\Group\LayerGroup';
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
        if (!$definition instanceof LayerGroup) {
            return;
        }

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
                }
            }
        }
    }
}
