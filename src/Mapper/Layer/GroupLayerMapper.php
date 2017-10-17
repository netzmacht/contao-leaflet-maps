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
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Group\FeatureGroup;
use Netzmacht\LeafletPHP\Definition\Group\LayerGroup;
use Netzmacht\LeafletPHP\Definition\Layer;

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
    protected function getClassName(Model $model, DefinitionMapper $mapper, Request $request = null)
    {
        if ($model->groupType === 'feature') {
            return FeatureGroup::class;
        }

        return LayerGroup::class;
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
