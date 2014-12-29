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

use Netzmacht\Contao\Leaflet\Model\MapModel;

/**
 * Class Content
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Content
{
    /**
     * Get all leaflet maps.
     *
     * @return array
     */
    public function getMaps()
    {
        $options    = array();
        $collection = MapModel::findAll();

        if ($collection) {
            foreach ($collection as $map) {
                $options[$map->id] = $map->title;
            }
        }

        return $options;
    }
}
