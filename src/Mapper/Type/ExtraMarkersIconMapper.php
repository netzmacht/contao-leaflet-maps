<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Type;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\DivIcon;

/**
 * Class DivIconMapper maps the icon model to the div icon definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Type
 */
class ExtraMarkersIconMapper extends AbstractIconMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Plugins\ExtraMarkers\ExtraMarkersIcon';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'extra';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addOptions(['icon', 'iconColor', 'markerColor', 'shape', 'number', 'prefix']);
    }

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

        if ($definition instanceof DivIcon && $model->iconSize) {
            $definition->setIconSize(explode(',', $model->iconSize, 2));
        }
    }
}
