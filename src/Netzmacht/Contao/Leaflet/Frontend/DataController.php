<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;


use Netzmacht\Contao\Leaflet\MapService;

class DataController
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
        $format = $this->input->get('format') ?: 'geojson';
        $type   = $this->input->get('type') ?: 'layer';
        $dataId = $this->input->get('id');

        try {
            list($data, $error) = $this->loadData($type, $dataId);
        } catch (\Exception $e) {
            if (\Config::get('debugMode') || \Config::get('displayErrors')) {
                throw $e;
            }
            $error = true;
        }

        if ($error) {
            header('HTTP/1.1 500 Internal Server Error');
        } else {
            $this->encodeData($format, $data);
        }
    }

    /**
     * @param $format
     * @param $data
     */
    public function encodeData($format, $data)
    {
        switch ($format) {
            case 'geojson':
                header('Content-Type: application/json');
                echo json_encode($data, JSON_UNESCAPED_SLASHES);
                break;
        }
    }

    /**
     * @param $type
     * @param $dataId
     *
     * @return array
     */
    public function loadData($type, $dataId)
    {
        $error = false;
        $data  = null;

        switch ($type) {
            case 'layer':
                $data = $this->mapService->getFeatureCollection($dataId);
                break;

            default:
                $error = true;

                return array($data, $error);
        }

        return array($data, $error);
    }
}
