<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet;

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
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function addJavascript($script, $type = self::TYPE_SOURCE)
    {
        switch ($type) {
            case static::TYPE_SOURCE:
                $GLOBALS['TL_HEAD'][] = sprintf('<script>%s</script>', $script);
                break;

            case static::TYPE_FILE:
                if (!\Config::get('debugMode')) {
                    $script .= '|static';
                }

                // no break

            default:
                $GLOBALS['TL_JAVASCRIPT'][] = $script;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function addStylesheet($stylesheet, $type = self::TYPE_FILE)
    {
        switch ($type) {
            case static::TYPE_SOURCE:
                $GLOBALS['TL_HEAD'][] = sprintf('<style>%s</style>', $stylesheet);
                break;

            case static::TYPE_FILE:
                if (!\Config::get('debugMode')) {
                    $stylesheet .= '|all|static';
                }
                // no break

            default:
                $GLOBALS['TL_CSS'][] = $stylesheet;
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
        $this->map = $map;

        return $this;
    }
}
