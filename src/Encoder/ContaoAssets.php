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

namespace Netzmacht\Contao\Leaflet\Encoder;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\LeafletPHP\Assets;

/**
 * Class ContaoAssets handles leaflet assets and integrate them into the Contao assets registry (Superglobals).
 *
 * @package Netzmacht\Contao\Leaflet
 */
class ContaoAssets implements Assets
{
    /**
     * The map javascript.
     *
     * @var string
     */
    private $map;

    /**
     * Assets manager.
     *
     * @var AssetsManager
     */
    private $assetsManager;

    /**
     * Cached assets.
     *
     * @var array
     */
    private $cache = [
        'stylesheets' => [],
        'javascripts' => [],
        'map'         => [],
    ];

    /**
     * ContaoAssets constructor.
     *
     * @param AssetsManager $assetsManager Contao assets manager.
     */
    public function __construct(AssetsManager $assetsManager)
    {
        $this->assetsManager = $assetsManager;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function addJavascript($script, $type = self::TYPE_SOURCE)
    {
        $this->cache['javascripts'][] = [$script, $type];

        switch ($type) {
            case static::TYPE_SOURCE:
                $GLOBALS['TL_HEAD'][] = sprintf('<script>%s</script>', $script);
                break;

            case static::TYPE_FILE:
            default:
                $this->assetsManager->addJavascript($script);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function addStylesheet($stylesheet, $type = self::TYPE_FILE)
    {
        $this->cache['stylesheets'][] = [$stylesheet, $type];

        switch ($type) {
            case static::TYPE_SOURCE:
                $GLOBALS['TL_HEAD'][] = sprintf('<style>%s</style>', $stylesheet);
                break;

            case static::TYPE_FILE:
            default:
                $this->assetsManager->addStylesheet($stylesheet);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * {@inheritdoc}
     */
    public function setMap($map)
    {
        $this->cache['map'] = $map;
        $this->map          = $map;

        return $this;
    }

    /**
     * Export to array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->cache;
    }

    /**
     * From array.
     *
     * @param array $cache Cache.
     *
     * @return void
     */
    public function fromArray(array $cache)
    {
        foreach ($cache['javascripts'] as $javascript) {
            $this->addJavascript($javascript[0], $javascript[1]);
        }

        foreach ($cache['stylesheets'] as $stylesheet) {
            $this->addStylesheet($stylesheet[0], $stylesheet[1]);
        }

        $this->map = $cache['map'];
    }
}
