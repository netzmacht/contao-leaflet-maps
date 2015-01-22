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

/**
 * Class ServiceContainerTrait provides simple access to the service container.
 *
 * @package Netzmacht\Contao\Leaflet
 */
trait ServiceContainerTrait
{
    /**
     * Get the service container.
     *
     * @return ServiceContainer
     */
    protected function getServiceContainer()
    {
        return $GLOBALS['container']['leaflet.service-container'];
    }
}
