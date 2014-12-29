<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;


class Layer
{
    public function getVariants($dataContainer)
    {
        if ($dataContainer->activeRecord
            && $dataContainer->activeRecord->tile_provider
            && !empty($GLOBALS['LEAFLET_TILE_PROVIDERS'][$dataContainer->activeRecord->tile_provider]['variants'])
        ) {
            return $GLOBALS['LEAFLET_TILE_PROVIDERS'][$dataContainer->activeRecord->tile_provider]['variants'];
        }

        return array();
    }
}
