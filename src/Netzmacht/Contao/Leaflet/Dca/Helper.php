<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;

use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Contao\Toolkit\Dca\Callback\GenerateAliasCallback;

/**
 * Helper class for dca functions.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Helper
{
    /**
     * Generate an alias callback which creates a valid javascript var name.
     *
     * @param string $table  The table.
     * @param string $column The value column.
     *
     * @return GenerateAliasCallback
     */
    public static function createGenerateAliasCallback($table, $column)
    {
        $callback = Dca::createGenerateAliasCallback($table, $column);
        $callback->getGenerator()->addFilter(
            function ($value) {
                return str_replace('-', '_', $value);
            }
        );

        return $callback;
    }
}
