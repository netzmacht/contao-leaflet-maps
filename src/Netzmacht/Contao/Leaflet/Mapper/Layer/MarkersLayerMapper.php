<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;


use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\GeoJsonMapper;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Group\LayerGroup;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\UI\Marker;

class MarkersLayerMapper extends AbstractLayerMapper implements GeoJsonMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Group\GeoJson';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'markers';

    protected function doBuild(
        Definition $definition,
        \Model $model,
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        if ($definition instanceof LayerGroup) {
            $collection = MarkerModel::findBy(
                array('active=1', 'pid=?'),
                array($model->id)
            );

            if ($collection) {
                foreach ($collection as $item) {
                    $marker = new Marker('marker_' . $item->id, $item->coordinates);
                    $marker->setTitle($item->tooltip);

                    $definition->addLayer($marker);
                }
            }
        }
    }

    /**
     * @param \Model           $model
     * @param DefinitionMapper $mapper
     * @param LatLngBounds     $bounds
     *
     * @return mixed
     */
    public function handleGeoJson(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $feature = new FeatureCollection();

        $collection = MarkerModel::findBy(
            array('active=1', 'pid=?'),
            array($model->id)
        );

        if ($collection) {
            foreach ($collection as $item) {
                $marker = new Marker('marker_' . $item->id, $item->coordinates);
                $marker->setTitle($item->tooltip);

                $feature->addFeature($marker->getFeature());
            }
        }

        return $feature;
    }
}
