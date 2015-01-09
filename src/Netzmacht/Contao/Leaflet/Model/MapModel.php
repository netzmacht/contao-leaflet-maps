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


class MapModel extends \Model
{
    protected static $strTable = 'tl_leaflet_map';

    /**
     * @return \Model\Collection
     */
    public function findLayers()
    {
        $query  = 'SELECT l.* FROM tl_leaflet_layer l LEFT JOIN tl_leaflet_map_layer m ON l.id = m.lid WHERE m.mid=?';
        $result = \Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        return \Model\Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }
}
