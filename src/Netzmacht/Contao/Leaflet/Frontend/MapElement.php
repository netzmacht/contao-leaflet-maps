<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;

/**
 * The content element for the leaflet map.
 *
 * @property int leaflet_map
 */
class MapElement extends AbstractMapHybrid
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_leaflet_map';

    /**
     * Get the identifier.
     *
     * @return string
     */
    protected function getIdentifier()
    {
        if ($this->get('leaflet_mapId')) {
            return $this->get('leaflet_mapId');
        }

        if ($this->get('cssID')[0]) {
            return 'map_' . $this->get('cssID')[0];
        }

        return 'map_ce_' . $this->get('id');
    }
}
