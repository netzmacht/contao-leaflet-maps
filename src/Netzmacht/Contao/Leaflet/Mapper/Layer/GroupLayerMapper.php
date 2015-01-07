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

class GroupLayerMapper extends AbstractLayerMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Group\LayerGroup';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'group';

    /**
     * {@inheritdoc}
     */
    protected function createInstance(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $class = $model->groupType === 'feature'
            ? 'Netzmacht\LeafletPHP\Definition\Group\FeatureGroup'
            : static::$definitionClass;

        $reflector = new \ReflectionClass($class);
        $instance  = $reflector->newInstanceArgs($this->buildConstructArguments($model, $mapper, $bounds));

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    protected function doBuild(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null
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
                /** @var Layer $layer */
                $layer = $mapper->handle($layerModel);
                $definition->addLayer($layer);
            }
        }
    }
}
