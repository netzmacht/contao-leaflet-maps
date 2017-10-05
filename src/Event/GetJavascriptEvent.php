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

namespace Netzmacht\Contao\Leaflet\Event;

use Netzmacht\LeafletPHP\Definition\Map;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetJavascriptEvent is emitted after the map javascript was created.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class GetJavascriptEvent extends Event
{
    const NAME = 'leaflet.service.get-javascript';

    /**
     * The generated javascript.
     *
     * @var string
     */
    private $javascript;

    /**
     * The map definition.
     *
     * @var Map
     */
    private $map;

    /**
     * Construct.
     *
     * @param Map    $map        The map definition.
     * @param string $javascript The generated javascript.
     */
    public function __construct($map, $javascript)
    {
        $this->map        = $map;
        $this->javascript = $javascript;
    }

    /**
     * Get the generated javascript.
     *
     * @return string
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * Get the map definition.
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }
}
