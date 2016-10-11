<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Control;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\ControlModel;

/**
 * Class LayersControlMapper maps the control model to the layers control definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
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
        Filter $filter = null,
        $elementId = null
    ) {
        $arguments    = parent::buildConstructArguments($model, $mapper, $filter, $elementId);
        $arguments[1] = array();
        $arguments[2] = array();

        /** @var ControlModel $model */
        $collection = $model->findActiveLayers();

        if ($collection) {
            foreach ($collection as $layer) {
                $argument = ($layer->controlMode === 'overlay') ? 2 : 1;

                $arguments[$argument][] = $mapper->handle($layer, $filter);
            }
        }

        return $arguments;
    }
}
