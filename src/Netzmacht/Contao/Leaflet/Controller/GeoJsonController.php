<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Controller;


use Netzmacht\Contao\Leaflet\MapService;

class GeoJsonController
{
    /**
     * @var MapService
     */
    private $mapService;

    /**
     * @var \Input
     */
    private $input;

    public function __construct(MapService $mapService, \Input $input)
    {
        $this->mapService = $mapService;
        $this->input      = $input;
    }

    public function execute()
    {
        $collection = $this->mapService->getFeatureCollection(\Input::get('id'));

        return json_encode($collection);
    }
}
