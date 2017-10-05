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

namespace Netzmacht\Contao\Leaflet\Filter;

/**
 * Interface Filter is designed for filter a data request. It just contains the filter information.
 *
 * Each layer has it own responsibility to apply the filter to their provided data.
 *
 * @package Netzmacht\Contao\Leaflet\Filter
 */
interface Filter
{
    /**
     * Get the name of the filter.
     *
     * @return string
     */
    public static function getName();

    /**
     * Create the filter from a request string.
     *
     * @param string $request The request.
     *
     * @return Filter
     */
    public static function fromRequest($request);

    /**
     * Create request string representation.
     *
     * @return string
     */
    public function toRequest();

    /**
     * Get the param values.
     *
     * @return array
     */
    public function getValues();
}
