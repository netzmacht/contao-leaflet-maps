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

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\LatLng;
use Netzmacht\LeafletPHP\Definition\Vector\Circle;

/**
 * Class CircleMapper maps the database model to the circle definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class CircleMapper extends AbstractVectorMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\Circle';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'circle';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addOption('radius');
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

        if ($definition instanceof Circle) {
            $definition->setLatLng(LatLng::fromString($model->coordinates));
        }
    }
}
