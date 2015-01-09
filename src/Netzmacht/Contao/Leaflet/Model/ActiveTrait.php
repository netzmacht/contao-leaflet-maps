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


trait ActiveTrait
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
     * @param int   $modelId
     * @param array $options
     *
     * @return \Model|null
     */
    public static function findActiveByPid($modelId, $options = array())
    {
        return static::findBy('active=1 AND pid', $modelId, $options);
    }

    public static function findActivated($options = array())
    {
        return static::findBy('active', '1', $options);
    }
}
