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

namespace Netzmacht\Contao\Leaflet\Frontend\InsertTag;

use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Toolkit\InsertTag\AbstractSingleInsertTagParser;
use Netzmacht\Contao\Toolkit\InsertTag\ArgumentParser;
use Netzmacht\Contao\Toolkit\InsertTag\ArgumentParserPlugin;

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
final class LeafletInsertTagParser extends AbstractSingleInsertTagParser
{
    use ArgumentParserPlugin;

    /**
     * Insert tag name.
     *
     * @var string
     */
    protected $tagName = 'leaflet';

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
    protected function parseTag(array $arguments, string $tag, string $raw)
    {
        if (empty($arguments['mapId'])) {
            return '';
        }

        $style    = empty($arguments['style']) ? 'width:400px;height:300px' : $arguments['style'];
        $template = empty($arguments['template']) ? 'leaflet_map_html' : $arguments['template'];

        return $this->generateMap($arguments['mapId'], $template, $style);
    }

    /**
     * {@inheritDoc}
     */
    protected function createArgumentParser(): ArgumentParser
    {
        return ArgumentParser::create()->splitBy('::', ['mapId', 'style', 'template']);
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
    private function generateMap($mapId, string $template, string $style)
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
