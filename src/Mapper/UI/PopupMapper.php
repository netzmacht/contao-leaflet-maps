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
use Netzmacht\Contao\Leaflet\Mapper\AbstractMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\UI\Popup;

/**
 * Class PopupMapper.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\UI
 */
class PopupMapper extends AbstractMapper
{
    /**
     * The definition class.
     *
     * @var string
     */
    protected static $definitionClass = Popup::class;

    /**
     * The model class.
     *
     * @var string
     */
    protected static $modelClass = PopupModel::class;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addConditionalOption('maxWidth')
            ->addConditionalOption('minWidth')
            ->addConditionalOption('maxHeight')
            ->addConditionalOption('className')
            ->addOptions('autoPan', 'keepInView', 'closeButton', 'zoomAnimation');
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
        parent::build($definition, $model, $mapper, $request, $parent);

        /** @var Popup $definition */
        /** @var PopupModel $model */

        $this->deserializePoint('offset', $definition, $model);

        if ($model->autoPan) {
            $padding = array_map(
                function ($value) {
                    return array_map('intval', trimsplit(',', $value));
                },
                deserialize($model->autoPanPadding, true)
            );

            if ($padding[0] === $padding[1]) {
                if (!empty($padding[0])) {
                    $definition->setAutoPanPadding($padding[0]);
                }
            } else {
                if ($padding[0]) {
                    $definition->setAutoPanPaddingTopLeft($padding[0]);
                }
                if ($padding[1]) {
                    $definition->setAutoPanPaddingBottomRight($padding[1]);
                }
            }
        }

        if (!$model->closeOnClick) {
            $definition->setCloseOnClick(false);
        }
    }

    /**
     * Deserialize point value and add it as option.
     *
     * @param string     $option     The option name.
     * @param Popup      $definition The popup definition.
     * @param PopupModel $model      The popup model.
     *
     * @return $this
     */
    protected function deserializePoint($option, Popup $definition, PopupModel $model)
    {
        if ($model->$option) {
            $setter = 'set' . ucfirst($option);
            $definition->$setter(array_map('intval', explode(',', $model->$option, 2)));
        }

        return $this;
    }
}
