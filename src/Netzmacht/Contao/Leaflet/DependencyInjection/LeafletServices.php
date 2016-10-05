<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\DependencyInjection;

use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;

/**
 * Class LeafletServices describes services provided by the leaflet package.
 *
 * @package Netzmacht\Contao\Leaflet\DependencyInjection
 */
class LeafletServices
{
    /**
     * Service name of the frontend value filter.
     *
     * @return ValueFilter
     */
    const FRONTEND_VALUE_FILTER = 'leaflet.frontend.value-filter';
}
