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

use Netzmacht\Contao\Toolkit\ServiceContainerTrait;
use Netzmacht\Contao\Leaflet\MapService;

/**
 * Class Hooks contains hooks for the frontend manipulation.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class Hooks
{
    use ServiceContainerTrait;

    /**
     * Replace the leaflet insert tag and returns the generated map.
     *
     * By default it creates the html template, so the script and html are rendered.
     *
     * Supported formats are:
     *  - {{leaflet::id|alias}}            The map id or alias.
     *  - {{leaflet::id::style}}           The style attribute, useful to pass the height and width of the container.
     *  - {{leaflet::id::style::template}} Optional template. Look at leaflet_map_js and leaflet_map_html as example.
     *
     * @param string $tag The given insert tag.
     *
     * @return bool|string
     *
     * @throws \Exception  If debug mode is enabled and anything went wrong.
     */
    public function replaceInsertTags($tag)
    {
        $parts = explode('::', $tag);

        if ($parts[0] !== 'leaflet' || empty($parts[1])) {
            return false;
        }

        $style    = empty($parts[2]) ? 'width:400px;height:300px' : $parts[2];
        $template = empty($parts[3]) ? 'leaflet_map_html' : $parts[3];

        return $this->generateMap($parts[1], $template, $style);
    }

    /**
     * Get the map service.
     *
     * @return MapService
     */
    protected function getMapService()
    {
        return static::getServiceContainer()->getService('leaflet.map.service');
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
            $mapService = $this->getMapService();

            return $mapService->generate($mapId, null, $mapId, $template, $style);
        } catch (\Exception $e) {
            if (static::getServiceContainer()->getConfig()->get('debugMode')) {
                throw $e;
            }

            return false;
        }
    }
}
