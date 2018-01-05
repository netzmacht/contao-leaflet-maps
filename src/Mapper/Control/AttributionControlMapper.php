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
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Control\Attribution;
use Netzmacht\LeafletPHP\Definition\Map;

/**
 * AttributionControlMapper maps the the attribution database definition to the definition class.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class AttributionControlMapper extends AbstractControlMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = Attribution::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'attribution';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addConditionalOption('prefix');
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
        if (!$definition instanceof Attribution) {
            return;
        }

        if ($model->disableDefault && $parent instanceof Map) {
            $parent->setAttributionControl(false);
        }

        $attributions = deserialize($model->attributions, true);

        foreach ($attributions as $attribution) {
            $definition->addAttribution($attribution);
        }
    }
}
