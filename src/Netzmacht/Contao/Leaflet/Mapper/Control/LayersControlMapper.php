<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Control;


use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

class LayersControlMapper extends AbstractControlMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Control\Layers';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'layers';

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        $arguments    = parent::buildConstructArguments($model, $mapper, $bounds, $elementId);
        $arguments[1] = array();
        $arguments[2] = array();

        $definition = $this->getLayersDefinition($model);
        $collection = LayerModel::findMultipleByIds(array_keys($definition));

        if ($collection) {
            foreach ($collection as $layer) {
                $argument = ($definition[$layer->id] === 'overlay') ? 2 : 1;

                $arguments[$argument][] = $mapper->handle($layer, $bounds);
            }
        }

        return $arguments;
    }

    /**
     * Get layers definition from the control model.
     *
     * @param \Model $model The control model.
     *
     * @return array
     */
    protected function getLayersDefinition(\Model $model)
    {
        $layers = deserialize($model->layers, true);
        $definition = array();

        foreach ($layers as $layer) {
            if ($layer['layer']) {
                $definition[$layer['layer']] = $layer['mode'];
            }
        }

        return $definition;
    }
}
