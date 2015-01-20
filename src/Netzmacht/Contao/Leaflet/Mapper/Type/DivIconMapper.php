<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Type;

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\DivIcon;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class DivIconMapper maps the icon model to the div icon definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Type
 */
class DivIconMapper extends AbstractIconMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Type\DivIcon';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'div';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->addOption('html');
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $bounds);

        if ($definition instanceof DivIcon && $model->iconSize) {
            $definition->setIconSize(explode(',', $model->iconSize, 2));
        }
    }
}
