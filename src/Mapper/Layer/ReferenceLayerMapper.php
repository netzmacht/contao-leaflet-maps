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

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition;

/**
 * Class ReferenceLayerMapper maps an reference layer to another layer.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class ReferenceLayerMapper extends AbstractLayerMapper
{
    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'reference';

    /**
     * {@inheritdoc}
     */
    public function handle(
        $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null,
        Definition $parent = null
    ) {
        $reference = LayerModel::findByPk($model->reference);

        if (!$reference || !$reference->active) {
            return null;
        }

        $elementId = $model->standalone ? $this->getElementId($model, $elementId) : null;

        return $mapper->handle($reference, $request, $elementId);
    }
}
