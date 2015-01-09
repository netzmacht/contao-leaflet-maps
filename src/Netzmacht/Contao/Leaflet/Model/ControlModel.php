<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Model;

class ControlModel extends AbstractActiveModel
{
    protected static $strTable = 'tl_leaflet_control';

    /**
     * @return \Model\Collection
     */
    public function findLayers()
    {
        $query  = 'SELECT l.*, c.mode as controlMode FROM tl_leaflet_layer l LEFT JOIN tl_leaflet_control_layer c ON l.id = c.lid WHERE c.cid=?';
        $result = \Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        return \Model\Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }

    /**
     * @return \Model\Collection
     */
    public function findActiveLayers()
    {
        $query  = 'SELECT l.*, c.mode as controlMode FROM tl_leaflet_layer l LEFT JOIN tl_leaflet_control_layer c ON l.id = c.lid WHERE c.cid=? AND l.active=1';
        $result = \Database::getInstance()
            ->prepare($query)
            ->execute($this->id);

        return \Model\Collection::createFromDbResult($result, 'tl_leaflet_layer');
    }
}
