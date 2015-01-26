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

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\MapService;

/**
 * The data controller handles ajax request for sub data.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class DataController
{
    /**
     * The map service.
     *
     * @var MapService
     */
    private $mapService;

    /**
     * The user input data.
     *
     * @var array
     */
    private $input = array(
        'format' => 'geojson',
        'type'   => 'layer',
        'id'     => null,
        'filter' => null,
        'values' => null
    );

    /**
     * Construct.
     *
     * @param MapService $mapService The map service.
     * @param array      $input      The user input as array.
     */
    public function __construct(MapService $mapService, $input)
    {
        $this->mapService = $mapService;
        $this->input      = array_merge($this->input, $input);
    }

    /**
     * Execute the controller and create the data response.
     *
     * @return void
     *
     * @throws \Exception If anything went wrong.
     */
    public function execute()
    {
        try {
            if ($this->input['filter']) {
                $filter = $this->createFilter();
            } else {
                $filter = null;
            }

            list($data, $error) = $this->loadData($this->input['type'], $this->input['id'], $filter);
            $this->encodeData($this->input['format'], $data);
        } catch (\Exception $e) {
            if (\Config::get('debugMode') || \Config::get('displayErrors')) {
                throw $e;
            }
            $error = true;
        }

        if ($error) {
            header('HTTP/1.1 500 Internal Server Error');
        }
    }

    /**
     * Encode the data.
     *
     * @param string $format The requested format.
     * @param mixed  $data   The given data.
     *
     * @return void
     */
    public function encodeData($format, $data)
    {
        switch ($format) {
            case 'geojson':
                header('Content-Type: application/json');
                echo json_encode($data, JSON_UNESCAPED_SLASHES);
                break;

            default:
                // Blame the code sniffer.
        }
    }

    /**
     * Load the data.
     *
     * @param string $type   The data type.
     * @param mixed  $dataId The data id.
     * @param Filter $filter Optional request filter.
     *
     * @return array
     */
    public function loadData($type, $dataId, Filter $filter = null)
    {
        $error = false;
        $data  = null;

        switch ($type) {
            case 'layer':
                $data = $this->mapService->getFeatureCollection($dataId, $filter);
                break;

            default:
                $error = true;

                return array($data, $error);
        }

        return array($data, $error);
    }

    /**
     * Create a filter.
     *
     * @return Filter
     * @throws \RuntimeException If the filter is not defined.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function createFilter()
    {
        if (!isset($GLOBALS['LEAFLET_FILTERS'][$this->input['filter']])) {
            throw new \RuntimeException(sprintf('Undefined filter "%s".', $this->input['filter']));
        }

        /** @var Filter $filter */
        $filter = $GLOBALS['LEAFLET_FILTERS'][$this->input['filter']];

        return $filter::fromRequest($this->input['values']);
    }
}
