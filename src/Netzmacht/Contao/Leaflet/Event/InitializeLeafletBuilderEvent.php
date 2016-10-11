<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
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
