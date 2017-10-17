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

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition\Vector\Rectangle;
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
    protected static $definitionClass = Rectangle::class;

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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        $latLngs = array_map(
            function ($latLng) {
                return LatLng::fromString($latLng);
            },
            deserialize($model->bounds, true)
        );

        $arguments   = parent::buildConstructArguments($model, $mapper, $request, $elementId);
        $arguments[] = new LatLngBounds($latLngs[0], $latLngs[1]);

        return $arguments;
    }
}
