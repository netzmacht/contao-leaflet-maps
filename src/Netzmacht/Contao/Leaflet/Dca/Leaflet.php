<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;


use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\MapMapper;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Definition\Type\LatLng;

class Leaflet
{
    /**
     * Validate a coordinate.
     *
     * @param $value
     *
     * @return mixed
     */
    public function validateCoordinate($value)
    {
        if (!empty($value)) {
            // Validate by creating latlng object. Throws an exception.

            LatLng::fromString($value);
        }

        return $value;
    }


    public function getZoomLevels()
    {
        return range(1, 20);
    }


    public function getGeocoder($dataContainer)
    {
        $template = new \BackendTemplate('be_leaflet_geocode');
        $template->field = 'ctrl_' . $dataContainer->field;

        try {
            $latLng = LatLng::fromString($dataContainer->value);
            $template->marker = $latLng->toJson();
        } catch(\Exception $e) {

        }


        return $template->parse();
    }

}
