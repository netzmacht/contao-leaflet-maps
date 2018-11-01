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

use Contao\Model\Collection;
use Netzmacht\Contao\Leaflet\Filter\BboxFilter;
use Netzmacht\Contao\Leaflet\Filter\DistanceFilter;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\LeafletPHP\Value\LatLngBounds;
use function var_dump;

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
     * @param int         $pid    The parent id.
     * @param Filter|null $filter The filter.
     *
     * @return Collection|null
     */
    public static function findByFilter($pid, ?Filter $filter = null)
    {
        if (!$filter) {
            return static::findActiveBy('pid', $pid, ['order' => 'sorting']);
        }

        switch (true) {
            case $filter instanceof BboxFilter:
                return static::findByBBoxFilter($pid, $filter);

            case $filter instanceof DistanceFilter:
                return static::findByDistanceFilter($pid, $filter);

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
     * @return Collection|null|MarkerModel[]
     */
    public static function findByBBoxFilter($pid, BboxFilter $filter)
    {
        $table   = static::getTable();
        $columns = [
            $table . '.active=1',
            $table . '.pid=?',
            $table . '.latitude > ? AND ' . $table . '.latitude < ?',
            $table . '.longitude > ? AND ' . $table . '.longitude < ?',
        ];

        /** @var LatLngBounds $bounds */
        $bounds = $filter->getValues()['bounds'];
        $values = [
            $pid,
            $bounds->getSouthWest()->getLatitude(),
            $bounds->getNorthEast()->getLatitude(),
            $bounds->getSouthWest()->getLongitude(),
            $bounds->getNorthEast()->getLongitude(),
        ];

        return static::findBy($columns, $values, ['order' => $table . '.sorting']);
    }

    /**
     * Find marker by distance filter.
     *
     * @param int            $pid    The layer id.
     * @param DistanceFilter $filter THe distance filter.
     *
     * @return Collection|null|MarkerModel[]
     */
    public static function findByDistanceFilter($pid, DistanceFilter $filter): ?Collection
    {
        $table = static::getTable();
        $query = <<<SQL
round(
  sqrt(
    power( 2 * pi() / 360 * (? - {$table}.latitude) * 6371,2)
    + power( 2 * pi() / 360 * (? - {$table}.longitude) * 6371 * COS( 2 * pi() / 360 * (? + {$table}.latitude) * 0.5 ),2)
  )
) <= ?
SQL;

        $center    = $filter->getCenter();
        $latitude  = $center->getLatitude();
        $longitude = $center->getLongitude();
        $values    = [$pid, $latitude, $longitude, $latitude, $filter->getRadius()];
        $columns   = [
            $table . '.active=1',
            $table . '.pid=?',
            $query
        ];

        return static::findBy($columns, $values, ['order' => $table . '.sorting']);
    }
}
