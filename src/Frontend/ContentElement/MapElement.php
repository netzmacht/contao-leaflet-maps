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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Frontend\ContentElement;

use Netzmacht\Contao\Leaflet\Frontend\AbstractMapHybrid;

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
    protected function getIdentifier(): string
    {
        if ($this->get('leaflet_mapId')) {
            return (string) $this->get('leaflet_mapId');
        }

        if ($this->get('cssID')[0]) {
            return 'map_' . $this->get('cssID')[0];
        }

        return 'map_ce_' . $this->get('id');
    }
}
