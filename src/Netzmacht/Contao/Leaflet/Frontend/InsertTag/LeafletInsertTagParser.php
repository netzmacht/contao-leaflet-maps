<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend\InsertTag;

use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Toolkit\InsertTag\Parser;

/**
 * LeafletInsertTagParser parses the leaflet insert tag.
 *
 * By default it creates the html template, so the script and html are rendered.
 *
 * Supported formats are:
 *  - {{leaflet::id|alias}}            The map id or alias.
 *  - {{leaflet::id::style}}           The style attribute, useful to pass the height and width of the container.
 *  - {{leaflet::id::style::template}} Optional template. Look at leaflet_map_js and leaflet_map_html as example.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\InsertTag
 */
class LeafletInsertTagParser implements Parser
{
    /**
     * The map service.
     *
     * @var MapProvider
     */
    private $mapProvider;

    /**
     * Debug mode.
     *
     * @var bool
     */
    private $debugMode;

    /**
     * LeafletInsertTagParser constructor.
     *
     * @param MapProvider $mapProvider Map provider.
     * @param bool        $debugMode   Debug mode.
     */
    public function __construct(MapProvider $mapProvider, $debugMode)
    {
        $this->mapProvider = $mapProvider;
        $this->debugMode   = $debugMode;
    }

    /**
     * {@inheritDoc}
     */
    public static function getTags()
    {
        return ['leaflet'];
    }

    /**
     * {@inheritDoc}
     */
    public function supports($tag)
    {
        return in_array($tag, static::getTags());
    }

    /**
     * {@inheritDoc}
     */
    public function parse($raw, $tag, $params = null, $cache = true)
    {
        $parts = explode('::', $params);

        if (empty($parts[0])) {
            return false;
        }

        $style    = empty($parts[1]) ? 'width:400px;height:300px' : $parts[1];
        $template = empty($parts[2]) ? 'leaflet_map_html' : $parts[2];

        return $this->generateMap($parts[0], $template, $style);
    }

    /**
     * Generate the map.
     *
     * @param string|int $mapId    The map id/alias.
     * @param string     $template The template.
     * @param string     $style    Optional style attribute.
     *
     * @return bool|string
     *
     * @throws \Exception If debug mode is enabled and something went wrong.
     */
    private function generateMap($mapId, $template, $style)
    {
        try {
            return $this->mapProvider->generate($mapId, null, $mapId, $template, $style);
        } catch (\Exception $e) {
            if ($this->debugMode) {
                throw $e;
            }
        }

        return false;
    }
}
