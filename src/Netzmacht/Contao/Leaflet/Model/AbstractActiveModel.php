<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Model;

/**
 * Class AbstractActiveModel is the base model for models with an active field.
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
abstract class AbstractActiveModel extends \Model
{
    /**
     * Find an active model by its model id.
     *
     * @param int   $modelId The model id.
     * @param array $options The query options.
     *
     * @return \Model|null
     */
    public static function findActiveByPK($modelId, $options = array())
    {
        return static::findOneBy('active=1 AND id', $modelId, $options);
    }

    /**
     * Find active models by a defined column.
     *
     * @param string|array $column  The query columns.
     * @param mixed        $value   The column value.
     * @param array        $options The options.
     *
     * @return \Model|null
     */
    public static function findActiveBy($column, $value, $options = array())
    {
        if (is_array($column)) {
            $column[] = 'active=1';
        } else {
            $column = 'active=1 AND ' . $column;
        }

        return static::findBy($column, $value, $options);
    }

    /**
     * Find collection activated models.
     *
     * @param array $options The query options.
     *
     * @return \Model\Collection|null
     */
    public static function findActives($options = array())
    {
        return static::findBy('active', '1', $options);
    }
}
