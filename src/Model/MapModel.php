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

namespace Netzmacht\Contao\Leaflet\Model;

use Model\Collection;

/**
 * Class MapModel for the tl_leaflet_map table.
 *
 * @property mixed|null locate
 * @property mixed|null title
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class MapModel extends \Model
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_map';

    /**
     * Find all related layers.
     *
     * @return Collection|null
     */
    public function findLayers()
    {
        $query  = 'SELECT l.* FROM tl_leaflet_layer l LEFT JOIN tl_leaflet_map_layer m ON l.id = m.lid WHERE m.mid=?';
        $result = \Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        if ($result->numRows < 1) {
            return null;
        }

        return Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }

    /**
     * Find all active layers.
     *
     * @return Collection|null
     */
    public function findActiveLayers()
    {
        $query = <<<SQL
SELECT    l.*
FROM      tl_leaflet_layer l
LEFT JOIN tl_leaflet_map_layer m
ON        l.id = m.lid
WHERE     m.mid=? AND l.active=1
SQL;

        $result = \Database::getInstance()->prepare($query)->execute($this->id);

        if ($result->numRows) {
            return Collection::createFromDbResult($result, 'tl_leaflet_layer');
        }

        return null;
    }
}
