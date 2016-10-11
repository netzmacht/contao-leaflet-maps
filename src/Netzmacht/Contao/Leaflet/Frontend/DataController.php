<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\MapProvider;

/**
 * The data controller handles ajax request for sub data.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class DataController
{
    /**
     * The map provider.
     *
     * @var MapProvider
     */
    private $mapProvider;

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
     * Filters configuration.
     * 
     * @var array
     */
    private $filters;

    /**
     * Display errors.
     *
     * @var bool
     */
    private $displayErrors;

    /**
     * Construct.
     *
     * @param MapProvider $mapProvider   The map provider.
     * @param array       $filters       Filters configuration.
     * @param bool        $displayErrors Display errors.
     */
    public function __construct(MapProvider $mapProvider, array $filters, $displayErrors)
    {
        $this->mapProvider   = $mapProvider;
        $this->filters       = $filters;
        $this->displayErrors = $displayErrors;
    }

    /**
     * Execute the controller and create the data response.
     *
     * @param array $input The user input as array.
     *
     * @return void
     *
     * @throws \Exception If anything went wrong.
     */
    public function execute(array $input)
    {
        $input = array_merge($this->input, $input);

        try {
            if ($input['filter']) {
                $filter = $this->createFilter($input);
            } else {
                $filter = null;
            }

            list($data, $error) = $this->loadData($input['type'], $input['id'], $filter);
            $this->encodeData($input['format'], $data);
        } catch (\Exception $e) {
            if ($this->displayErrors) {
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
                $data = $this->mapProvider->getFeatureCollection($dataId, $filter);
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
     * @param array $input The user input as array.
     *
     * @return Filter
     * @throws \RuntimeException If the filter is not defined.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function createFilter($input)
    {
        if (!isset($this->filters[$input['filter']])) {
            throw new \RuntimeException(sprintf('Undefined filter "%s".', $input['filter']));
        }

        /** @var Filter $filter */
        $filter = $this->filters[$input['filter']];

        return $filter::fromRequest($input['values']);
    }
}
