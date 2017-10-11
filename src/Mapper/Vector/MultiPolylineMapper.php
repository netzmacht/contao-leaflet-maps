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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $request);

        if ($definition instanceof Polyline) {
            $this->createLatLngs($definition, $model);
        }
    }

    /**
     * Create lat lngs for the definition.
     *
     * @param Polyline $definition The multi polyline.
     * @param Model    $model      The definition model.
     *
     * @return void
     */
    protected function createLatLngs(Polyline $definition, Model $model)
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
