<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Value\LatLng;
use Netzmacht\LeafletPHP\Definition\Vector\Polyline;

/**
 * Class PolylineMapper maps the database model to the polyline definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class PolylineMapper extends AbstractVectorMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\Polyline';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'polyline';

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

        if ($definition instanceof Polyline) {
            array_map(
                function ($row) use ($definition) {
                    $definition->addLatLng(LatLng::fromString($row));
                },
                explode("\n", $model->data)
            );
        }
    }
}
