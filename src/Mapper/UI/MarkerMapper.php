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

namespace Netzmacht\Contao\Leaflet\Mapper\UI;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\AbstractMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;
use Netzmacht\LeafletPHP\Definition\UI\Marker;
use Netzmacht\LeafletPHP\Definition\UI\Popup;

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
    protected static $modelClass = MarkerModel::class;

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = Marker::class;

    /**
     * Frontend filter.
     *
     * @var ValueFilter
     */
    protected $valueFilter;

    /**
     * Construct.
     *
     * @param ValueFilter $valueFilter Frontend filter.
     */
    public function __construct(ValueFilter $valueFilter)
    {
        parent::__construct();

        $this->valueFilter = $valueFilter;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        $arguments   = parent::buildConstructArguments($model, $mapper, $request, $elementId);
        $arguments[] = [$model->latitude, $model->longitude, $model->altitude ?: null] ?: null;

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->optionsBuilder
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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        if ($definition instanceof Marker) {
            if ($model->addPopup) {
                $popup   = null;
                $content = $this->valueFilter->filter($model->popupContent);

                if ($model->popup) {
                    $popupModel = PopupModel::findActiveByPK($model->popup);

                    if ($popupModel) {
                        $popup = $mapper->handle($popupModel, $request, null, $definition);
                    }
                }

                if ($popup instanceof Popup) {
                    $definition->bindPopup($content, $popup->getOptions());
                } else {
                    $definition->bindPopup($content);
                }
            }

            if ($model->customIcon) {
                $iconModel = IconModel::findBy(
                    ['id=?', 'active=1'],
                    [$model->icon],
                    ['return' => 'Model']
                );

                if ($iconModel) {
                    /** @var ImageIcon $icon */
                    $icon = $mapper->handle($iconModel);
                    $definition->setIcon($icon);
                }
            }
        }
    }
}
