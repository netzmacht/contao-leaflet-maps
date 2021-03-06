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

namespace Netzmacht\Contao\Leaflet\Mapper\Control;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\LeafletPHP\Definition\Control\Layers;

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
    protected static $definitionClass = Layers::class;

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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        $arguments    = parent::buildConstructArguments($model, $mapper, $request, $elementId);
        $arguments[1] = [];
        $arguments[2] = [];

        /** @var ControlModel $model */
        $collection = $model->findActiveLayers();

        if ($collection) {
            foreach ($collection as $layer) {
                $argument = ($layer->controlMode === 'overlay') ? 2 : 1;

                $arguments[$argument][] = $mapper->handle($layer, $request);
            }
        }

        return $arguments;
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addOptions(['collapsed', 'autoZIndex']);
    }
}
