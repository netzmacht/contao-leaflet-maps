<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Frontend;

/**
 * The frontend module for the Leaflet map.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class MapModule extends AbstractMapHybrid
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'mod_leaflet_map';

    /**
     * Get the identifier.
     *
     * @return string
     */
    protected function getIdentifier(): string
    {
        if ($this->get('leaflet_mapId')) {
            return $this->get('leaflet_mapId');
        }

        if ($this->get('cssID')[0]) {
            return 'map_' . $this->get('cssID')[0];
        }

        return 'map_mod_' . $this->get('id');
    }
}
