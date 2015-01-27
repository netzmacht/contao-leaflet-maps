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
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Leaflet\Model\StyleModel;
use Netzmacht\Contao\Leaflet\ServiceContainerTrait;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\HasPopup;
use Netzmacht\LeafletPHP\Definition\UI\Popup;
use Netzmacht\LeafletPHP\Definition\Vector\Path;

/**
 * Class AbstractVectorMapper is the base class for the vector model definition mapping.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class AbstractVectorMapper extends AbstractTypeMapper
{
    use ServiceContainerTrait;

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
        Filter $filter = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $filter);

        if ($definition instanceof Path && $model->style) {
            $styleModel = StyleModel::findActiveByPk($model->style);

            if ($styleModel) {
                $style = $mapper->handle($styleModel);

                if ($style instanceof Style) {
                    $style->apply($definition);
                }
            }
        }

        $this->buildPopup($definition, $model, $mapper, $filter);
    }

    /**
     * Build the popup.
     *
     * @param Definition       $definition The definition.
     * @param \Model           $model      The model.
     * @param DefinitionMapper $mapper     The definition mapper.
     * @param Filter           $filter     The filter.
     *
     * @return void
     */
    protected function buildPopup(Definition $definition, \Model $model, DefinitionMapper $mapper, Filter $filter)
    {
        if ($definition instanceof HasPopup && $model->addPopup) {
            $popup   = null;
            $content = $this
                ->getServiceContainer()
                ->getFrontendValueFilter()
                ->filter($model->popupContent);

            if ($model->popup) {
                $popupModel = PopupModel::findActiveByPK($model->popup);

                if ($popupModel) {
                    $popup = $mapper->handle($popupModel, $filter, null, $definition);
                }
            }

            if ($popup instanceof Popup) {
                $definition->bindPopup($content, $popup->getOptions());
            } else {
                $definition->bindPopup($content);
            }
        }
    }
}
