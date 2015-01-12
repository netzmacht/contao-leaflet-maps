<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Model;

use Model\Collection;

/**
 * Class MapModel for the tl_leaflet_map table.
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
