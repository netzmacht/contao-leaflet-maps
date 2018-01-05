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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener;

use Netzmacht\Contao\Leaflet\Frontend\Assets\LibrariesConfiguration;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Leaflet;

/**
 * Class RegisterLibrariesListener.
 *
 * @package Netzmacht\Contao\Leaflet\Listener
 */
final class RegisterLibrariesListener
{
    /**
     * Libraries configuration.
     *
     * @var LibrariesConfiguration
     */
    private $libraries;

    /**
     * Leaflet builder.
     *
     * @var Leaflet
     */
    private $leaflet;

    /**
     * RegisterLibrariesListener constructor.
     *
     * @param LibrariesConfiguration $libraries Libraries configuration.
     * @param Leaflet                $leaflet   Leaflet builder.
     */
    public function __construct(LibrariesConfiguration $libraries, Leaflet $leaflet)
    {
        $this->libraries = $libraries;
        $this->leaflet   = $leaflet;
    }

    /**
     * Handle the on initialize system hook.
     *
     * @return void
     */
    public function onInitializeSystem()
    {
        foreach ($this->libraries as $name => $assets) {
            if (!empty($assets['css'])) {
                list ($source, $type) = (array) $assets['css'];
                $this->leaflet->registerStylesheet($name, $source, $type ?: Assets::TYPE_FILE);
            }
            if (!empty($assets['javascript'])) {
                list ($source, $type) = (array) $assets['javascript'];
                $this->leaflet->registerJavascript($name, $source, $type ?: Assets::TYPE_FILE);
            }
        }
    }
}
