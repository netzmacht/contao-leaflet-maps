<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Dca;

use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory;
use Netzmacht\LeafletPHP\Value\LatLng;

/**
 * Class Leaflet is the base helper providing different methods.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class LeafletCallbacks
{
    /**
     * File system.
     *
     * @var \Files
     */
    private $fileSystem;

    /**
     * LeafletCallbacks constructor.
     *
     * @param \Files $fileSystem File system.
     */
    public function __construct(\Files $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Generate the callback definition.
     *
     * @param string $methodName Callback method name.
     *
     * @return callable
     */
    public static function callback($methodName)
    {
        return CallbackFactory::service('leaflet.dca.common', $methodName);
    }

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

    /**
     * Clear the leaflet cache.
     *
     * @param mixed $value Value when used as save_callback.
     *
     * @return mixed
     */
    public function clearCache($value = null)
    {
        $this->fileSystem->rrdir('system/cache/leaflet', true);

        return $value;
    }
}
