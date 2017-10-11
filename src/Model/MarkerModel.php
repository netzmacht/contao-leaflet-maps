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

use Netzmacht\Contao\Leaflet\Filter\BboxFilter;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\LeafletPHP\Value\LatLngBounds;

/**
 * Class MarkerModel for the tl_leaflet_marker table.
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class MarkerModel extends AbstractActiveModel
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_marker';

    /**
     * Find by a filter.
     *
     * @param int    $pid    The parent id.
     * @param Filter $filter The filter.
     *
     * @return \Model\Collection|null
     */
    public static function findByFilter($pid, Filter $filter = null)
    {
        if (!$filter) {
            return static::findActiveBy('pid', $pid, array('order' => 'sorting'));
        }

        switch ($filter->getName()) {
            case 'bbox':
                return static::findByBBoxFilter($pid, $filter);

            default:
                return null;
        }
    }

    /**
     * Find by the bbox filter.
     *
     * @param int        $pid    The layer id.
     * @param BboxFilter $filter The bbox filter.
     *
     * @return \Model\Collection|null
     */
    public static function findByBBoxFilter($pid, BboxFilter $filter)
    {
        $columns = array(
            'active=1',
            'pid=?',
            'latitude > ? AND latitude < ?',
            'longitude > ? AND longitude < ?'
        );

        /** @var LatLngBounds $bounds */
        $bounds = $filter->getValues()['bounds'];
        $values = array(
            $pid,
            $bounds->getSouthWest()->getLatitude(),
            $bounds->getNorthEast()->getLatitude(),
            $bounds->getSouthWest()->getLongitude(),
            $bounds->getNorthEast()->getLongitude()
        );

        return static::findBy($columns, $values, array('order' => 'sorting'));
    }
}
