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
use Netzmacht\LeafletPHP\Definition\Vector\Polyline;

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


    protected function doBuild(
        Definition $definition,
        \Model $model,
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        parent::doBuild($definition, $model, $builder, $bounds);

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
