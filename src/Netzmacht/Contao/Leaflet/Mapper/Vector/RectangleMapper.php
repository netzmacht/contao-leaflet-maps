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
use Netzmacht\LeafletPHP\Value\LatLng;
use Netzmacht\LeafletPHP\Value\LatLngBounds;

/**
 * Class RectangleMapper maps a database model to its rectangle vector definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class RectangleMapper extends AbstractVectorMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\Rectangle';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'rectangle';

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        $elementId = null
    ) {
        $latLngs = array_map(
            function ($latLng) {
                return LatLng::fromString($latLng);
            },
            deserialize($model->bounds, true)
        );

        $arguments   = parent::buildConstructArguments($model, $mapper, $filter, $elementId);
        $arguments[] = new LatLngBounds($latLngs[0], $latLngs[1]);

        return $arguments;
    }
}
