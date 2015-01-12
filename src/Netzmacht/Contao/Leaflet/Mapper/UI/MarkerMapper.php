<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\UI;

use Netzmacht\Contao\Leaflet\Mapper\AbstractMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\UI\Marker;

/**
 * Class MarkerMapper maps the marker model to the marker definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\UI
 */
class MarkerMapper extends AbstractMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\MarkerModel';

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\UI\Marker';

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        $arguments   = parent::buildConstructArguments($model, $mapper, $bounds, $elementId);
        $arguments[] = $model->coordinates ?: null;

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this
            ->addConditionalOption('tooltip', 'title', 'tooltip')
            ->addConditionalOption('alt')
            ->addConditionalOption('zIndexOffset')
            ->addOptions('clickable', 'keyboard', 'draggable');
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        if ($definition instanceof Marker) {
            if ($model->addPopup) {
                $definition->bindPopup($model->popupContent);
            }

            if ($model->customIcon) {
                $iconModel = IconModel::findBy(
                    array('id=?', 'active=1'),
                    array($model->icon),
                    array('return' => 'Model')
                );

                if ($iconModel) {
                    /** @var ImageIcon $icon */
                    $icon = $builder->handle($iconModel);
                    $definition->setIcon($icon);
                }
            }
        }
    }
}
