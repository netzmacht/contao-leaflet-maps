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
     * The user input object.
     *
     * @var \Input
     */
    private $input;

    /**
     * Construct.
     *
     * @param MapService $mapService The map service.
     * @param \Input     $input      The user input object.
     */
    public function __construct(MapService $mapService, \Input $input)
    {
        $this->mapService = $mapService;
        $this->input      = $input;
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
        $format = $this->getInput('format', 'geojson');
        $type   = $this->getInput('type', 'layer');
        $dataId = $this->getInput('id');
        $page   = \Input::get('page', true);

        if ($page) {
            // Fake a page request.
            \Environment::set('request', base64_decode($page));

            // We need the auto_item being set. So call the getPageIdFromUrl method, this does it internally.
            \Frontend::getPageIdFromUrl();
        }

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
     * Enocode the data.
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

    /**
     * Get an input value.
     *
     * @param string $name    The input name.
     * @param mixed  $default Optional a default value if empty.
     *
     * @return string
     */
    protected function getInput($name, $default = null)
    {
        return $this->input->get($name) ?: $default;
    }
}
