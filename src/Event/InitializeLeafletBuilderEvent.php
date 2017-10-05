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

use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InitializeLeafletBuilderEvent is emitted when the leaflet builder is created.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class InitializeLeafletBuilderEvent extends Event
{
    const NAME = 'leaflet.boot.initialize-leaflet-builder';

    /**
     * The leaflet builder.
     *
     * @var Leaflet
     */
    private $builder;

    /**
     * Construct.
     *
     * @param Leaflet $builder The leaflet builder.
     */
    public function __construct(Leaflet $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the builder.
     *
     * @return Leaflet
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
