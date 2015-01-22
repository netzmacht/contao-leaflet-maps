<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet;

use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;

/**
 * Class ServiceContainer
 *
 * @package Netzmacht\Contao\Leaflet
 */
class ServiceContainer
{
    /**
     * The global service container.
     *
     * @var \Pimple
     */
    private $container;

    /**
     * Construct.
     *
     * @param \Pimple $container The global service container.
     */
    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    /**
     * Get the value filter service.
     *
     * @return ValueFilter
     */
    public function getFrontendValueFilter()
    {
        return $this->getService('leaflet.frontend.value-filter');
    }

    /**
     * Get a service from the container.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function getService($name)
    {
        return $this->container[$name];
    }
}
