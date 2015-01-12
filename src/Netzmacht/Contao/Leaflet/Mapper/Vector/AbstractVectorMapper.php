<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

use Netzmacht\Contao\Leaflet\Definition\Style;
use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\StyleModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\HasPopup;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\Vector\Path;

/**
 * Class AbstractVectorMapper is the base class for the vector model definition mapping.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class AbstractVectorMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\VectorModel';

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null
    ) {
        parent::build($definition, $model, $mapper, $bounds);

        if ($definition instanceof Path && $model->style) {
            $styleModel = StyleModel::findActiveByPk($model->style);

            if ($styleModel) {
                $style = $mapper->handle($styleModel);

                if ($style instanceof Style) {
                    $style->apply($definition);
                }
            }
        }

        if ($definition instanceof HasPopup && $model->addPopup) {
            $definition->bindPopup($model->popupContent);
        }
    }
}
