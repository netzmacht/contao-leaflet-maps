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
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class ReferenceLayerMapper maps an reference layer to another
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
    public function handle($model, DefinitionMapper $mapper, LatLngBounds $bounds = null, $elementId = null)
    {
        $reference = LayerModel::findByPk($model->reference);

        if (!$reference || !$reference->active) {
            return null;
        }

        return $mapper->handle($reference, $bounds, $this->getElementId($model, $elementId));
    }
}
