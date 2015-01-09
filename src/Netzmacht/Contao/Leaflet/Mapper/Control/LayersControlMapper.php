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
use Netzmacht\Contao\Leaflet\Model\ControlModel;
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

        /** @var ControlModel $model */
        $collection = $model->findLayers();

        if ($collection) {
            foreach ($collection as $layer) {
                $argument = ($layer->controlMode === 'overlay') ? 2 : 1;

                $arguments[$argument][] = $mapper->handle($layer, $bounds);
            }
        }

        return $arguments;
    }
}
