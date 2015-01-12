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
use Netzmacht\LeafletPHP\Definition\Vector\MultiPolyline;
use Netzmacht\LeafletPHP\Definition\Vector\Polyline;

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
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Vector\MultiPolyline';

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
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        parent::build($definition, $model, $builder, $bounds);

        if ($definition instanceof MultiPolyline) {
            $this->createLatLngs($definition, $model);
        }
    }

    /**
     * Create lat lngs for the definition.
     *
     * @param MultiPolyline $definition The multi polyline.
     * @param \Model        $model      The definition model.
     *
     * @return void
     */
    protected function createLatLngs(MultiPolyline $definition, \Model $model)
    {
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
