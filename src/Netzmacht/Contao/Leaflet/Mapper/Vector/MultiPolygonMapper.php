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
use Netzmacht\LeafletPHP\Definition\Vector\MultiPolygon;

/**
 * Class MultiPolygonMapper maps the multi polygon database model to its definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class MultiPolygonMapper extends MultiPolylineMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\MultiPolygon';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'multiPolygon';

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

        if ($definition instanceof MultiPolygon) {
            $this->createLatLngs($definition, $model);
        }
    }
}
