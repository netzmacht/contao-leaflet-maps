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

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\Controller;
use Contao\DataContainer;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\LeafletPHP\Value\LatLng;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Leaflet is the base helper providing different methods.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class LeafletDcaListener
{
    /**
     * File system.
     *
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * Cache dir.
     *
     * @var string
     */
    private $cacheDir;

    /**
     * LeafletCallbacks constructor.
     *
     * @param Filesystem $fileSystem File system.
     * @param string     $cacheDir   Cache dir.
     */
    public function __construct(Filesystem $fileSystem, string $cacheDir)
    {
        $this->fileSystem = $fileSystem;
        $this->cacheDir   = $cacheDir;
    }

    /**
     * Load the language files.
     *
     * @return void
     */
    public function loadLanguageFile()
    {
        Controller::loadLanguageFile('leaflet');
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
     * @param DataContainer $dataContainer The dataContainer driver.
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
            // LatLng throws an exception of value could not be created. Just let the value empty when.
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
        $this->fileSystem->remove($this->cacheDir);

        return $value;
    }
}
