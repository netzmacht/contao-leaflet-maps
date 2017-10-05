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
use Netzmacht\LeafletPHP\Definition\Vector\Polyline;
use Netzmacht\LeafletPHP\Value\LatLng;

/**
 * Class MultiPolylineMapper maps the databse model it the multi polyline definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class MultiPolylineMapper extends AbstractVectorMapper
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
    protected static $type = 'multiPolyline';

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
            $this->createLatLngs($definition, $model);
        }
    }

    /**
     * Create lat lngs for the definition.
     *
     * @param Polyline $definition The multi polyline.
     * @param \Model   $model      The definition model.
     *
     * @return void
     */
    protected function createLatLngs(Polyline $definition, \Model $model)
    {
        foreach (deserialize($model->multiData, true) as $ring => $data) {
            $latLngs = array_map(
                function ($row) {
                    return LatLng::fromString($row);
                },
                explode("\n", $data)
            );

            $definition->addLatLngs($latLngs, $ring);
        }
    }
}
