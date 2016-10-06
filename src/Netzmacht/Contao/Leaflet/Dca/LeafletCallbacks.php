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
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Value\LatLng;

/**
 * Class Leaflet is the base helper providing different methods.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class LeafletCallbacks
{
    /**
     * Create the zoom range.
     *
     * @return array
     */
    public function getZoomLevels()
    {
        return range(1, 20);
    }

    /**
     * Get the geocoder wizard.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return string
     */
    public function getGeocoder($dataContainer)
    {
        $template        = new \BackendTemplate('be_leaflet_geocode');
        $template->field = 'ctrl_' . $dataContainer->field;

        try {
            $latLng           = LatLng::fromString($dataContainer->value);
            $template->marker = json_encode($latLng);
        } catch (\Exception $e) {
            // LatLng throws an exeption of value could not be created. Just let the value empty when.
        }


        return $template->parse();
    }

    /**
     * Get all layers.
     *
     * @return array
     */
    public function getLayers()
    {
        $options    = array();
        $collection = LayerModel::findBy('pid', '0', array('order' => 'title'));

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = $model->title;
            }
        }

        return $options;
    }
}
