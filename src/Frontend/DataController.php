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

namespace Netzmacht\Contao\Leaflet\Frontend;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Filter\FilterFactory;
use Netzmacht\Contao\Leaflet\MapProvider;

/**
 * The data controller handles ajax request for sub data.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class DataController
{
    /**
     * The user input data.
     *
     * @var array
     */
    private $input = [
        'format' => 'geojson',
        'type'   => 'layer',
        'id'     => null,
        'filter' => null,
        'values' => null,
    ];

    /**
     * Debug mode.
     *
     * @var bool
     */
    private $debugMode;

    /**
     * Filter factory.
     *
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * Construct.
     *
     * @param FilterFactory $filterFactory Filter factory.
     * @param bool          $debugMode     Debug mode.
     */
    public function __construct(FilterFactory $filterFactory, $debugMode)
    {
        $this->debugMode     = $debugMode;
        $this->filterFactory = $filterFactory;
    }

    /**
     * Execute the controller and create the data response.
     *
     * @param array       $input       The user input as array.
     * @param MapProvider $mapProvider Map provider.
     *
     * @return void
     * @throws \Exception If anything went wrong.
     */
    public function execute(array $input, MapProvider $mapProvider)
    {
        $input = array_merge($this->input, $input);

        try {
            if ($input['filter']) {
                $filter = $this->filterFactory->create($input['filter'], $input['values']);
            } else {
                $filter = null;
            }

            list($data, $error) = $this->loadData($mapProvider, $input['type'], $input['id'], $filter);
            $this->encodeData($input['format'], $data);
        } catch (\Exception $e) {
            if ($this->debugMode) {
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
     * @param MapProvider $mapProvider Map provider.
     * @param string      $type        The data type.
     * @param mixed       $dataId      The data id.
     * @param Filter      $filter      Optional request filter.
     *
     * @return array
     */
    public function loadData(MapProvider $mapProvider, $type, $dataId, Filter $filter = null)
    {
        $error = false;
        $data  = null;

        switch ($type) {
            case 'layer':
                $data = $mapProvider->getFeatureCollection($dataId, $filter);
                break;

            default:
                $error = true;

                return [$data, $error];
        }

        return [$data, $error];
    }
}
