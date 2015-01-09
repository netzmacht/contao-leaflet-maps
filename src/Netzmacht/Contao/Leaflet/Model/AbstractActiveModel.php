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


abstract class AbstractActiveModel extends \Model
{
    /**
     *
     * @param int   $modelId
     * @param array $options
     *
     * @return \Model|null
     */
    public static function findActiveByPK($modelId, $options = array())
    {
        return static::findOneBy('active=1 AND id', $modelId, $options);
    }

    /**
     *
     * @param int   $value
     * @param array $options
     *
     * @return \Model|null
     */
    public static function findActiveBy($column, $value, $options = array())
    {
        return static::findBy('active=1 AND ' . $column, $value, $options);
    }

    public static function findActivated($options = array())
    {
        return static::findBy('active', '1', $options);
    }
}
