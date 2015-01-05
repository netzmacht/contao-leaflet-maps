<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper;


abstract class AbstractTypeMapper extends AbstractMapper
{
    protected static $type;

    public function match(\Model $model)
    {
        return parent::match($model) && $model->type === static::$type;
    }
}
