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

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\System;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\LeafletPHP\Value\LatLng;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

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
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * System adapter.
     *
     * @var Adapter|System
     */
    private $systemAdapter;

    /**
     * LeafletCallbacks constructor.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param TemplateEngine    $templateEngine    Template engine.
     * @param Filesystem        $fileSystem        File system.
     * @param Adapter|System    $systemAdapter     Contao system adapter.
     * @param string            $cacheDir          Cache dir.
     */
    public function __construct(
        RepositoryManager $repositoryManager,
        TemplateEngine $templateEngine,
        Filesystem $fileSystem,
        $systemAdapter,
        string $cacheDir
    ) {
        $this->repositoryManager = $repositoryManager;
        $this->templateEngine    = $templateEngine;
        $this->fileSystem        = $fileSystem;
        $this->systemAdapter     = $systemAdapter;
        $this->cacheDir          = $cacheDir;
    }

    /**
     * Load the language files.
     *
     * @return void
     */
    public function loadLanguageFile()
    {
        $this->systemAdapter->loadLanguageFile('leaflet');
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
        $data['field'] = 'ctrl_' . $dataContainer->field;

        try {
            $latLng         = LatLng::fromString($dataContainer->value);
            $data['marker'] = json_encode($latLng);
        } catch (\Exception $e) {
            // LatLng throws an exception of value could not be created. Just let the value empty when.
            $data['marker'] = null;
        }

        return $this->templateEngine->render('toolkit:be:be_leaflet_geocode.html5', $data);
    }

    /**
     * Get all layers.
     *
     * @return array
     */
    public function getLayers()
    {
        $options    = [];
        $repository = $this->repositoryManager->getRepository(LayerModel::class);
        $collection = $repository->findBy(['pid=?'], ['0'], ['order' => 'title']);

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
