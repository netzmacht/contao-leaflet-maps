<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Alias;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Class UnderscoreFilter
 *
 * @package Netzmacht\Contao\Leaflet\Alias
 */
class UnderscoreFilter extends Filter\AbstractFilter
{
    /**
     * {@inheritDoc}
     */
    public function repeatUntilValid()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, $value, $separator)
    {
        return str_replace('-', '_', $value);
    }
}
