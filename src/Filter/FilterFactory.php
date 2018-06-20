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

namespace Netzmacht\Contao\Leaflet\Filter;

/**
 * Class FilterFactory.
 *
 * @package Netzmacht\Contao\Leaflet\Filter
 */
final class FilterFactory
{
    /**
     * Map of filter classes.
     *
     * @var array
     */
    private $filters;

    /**
     * FilterFactory constructor.
     *
     * @param array $filters Map of filter classes.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Create a filter.
     *
     * @param string $filter Filter name.
     * @param string $values Filter values.
     *
     * @return Filter
     *
     * @throws \RuntimeException When filter is not supported.
     */
    public function create(string $filter, string $values): Filter
    {
        if (isset($this->filters[$filter])) {
            return call_user_func([$this->filters[$filter], 'fromRequest'], $values);
        }

        throw new \RuntimeException(sprintf('Creating filter failed. Unsupported filter "%s"', $filter));
    }
}
