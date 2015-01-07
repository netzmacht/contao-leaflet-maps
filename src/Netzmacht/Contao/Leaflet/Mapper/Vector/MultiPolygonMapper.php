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


use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\LatLng;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\Vector\MultiPolygon;

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

    protected function doBuild(
        Definition $definition,
        \Model $model,
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        parent::doBuild($definition, $model, $builder, $bounds);

        if ($definition instanceof MultiPolygon) {
            $latLngs = array();

            foreach (deserialize($model->multiData, true) as $data) {
                $latLngs[] = array_map(
                    function ($row) {
                        return LatLng::fromString($row);
                    },
                    explode("\n", $data)
                );
            }

            $definition->setLatLngs($latLngs);
        }
    }
}
