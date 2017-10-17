<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\Contao\Leaflet\Filter\Filter;

/**
 * Class MapRequest
 *
 * @package Netzmacht\Contao\Leaflet\Request
 */
class Request
{
    /**
     * Map identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Request filter.
     *
     * @var Filter|null
     */
    private $filter;

    /**
     * Request constructor.
     *
     * @param string      $identifier Map identifier.
     * @param Filter|null $filter     Filter.
     */
    public function __construct($identifier, Filter $filter = null)
    {
        $this->identifier = $identifier;
        $this->filter     = $filter;
    }

    /**
     * Get the map identifier.
     *
     * @return string
     */
    public function getMapIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the filter.
     *
     * @return Filter|null
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
