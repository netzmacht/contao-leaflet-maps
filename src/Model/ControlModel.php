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

namespace Netzmacht\Contao\Leaflet\Model;

use Contao\Database;
use Contao\Model\Collection;

/**
 * Class ControlModel for the tl_leaflet_vector table.
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class ControlModel extends AbstractActiveModel
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_control';

    /**
     * Find all related layers.
     *
     * @return Collection|null
     */
    public function findLayers()
    {
        $query = <<<SQL
SELECT    l.*, c.mode as controlMode
FROM      tl_leaflet_layer l
LEFT JOIN tl_leaflet_control_layer c ON l.id = c.lid
WHERE     c.cid=?
SQL;

        $result = Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        if ($result->numRows < 1) {
            return null;
        }

        return Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }

    /**
     * Find active layers.
     *
     * @return Collection|null
     */
    public function findActiveLayers()
    {
        $query = <<<SQL
SELECT    l.*, c.mode as controlMode
FROM      tl_leaflet_layer l
LEFT JOIN tl_leaflet_control_layer
c ON      l.id = c.lid
WHERE     c.cid=? AND l.active=1
SQL;

        $result = Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        if ($result->numRows < 1) {
            return null;
        }

        return Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }
}
