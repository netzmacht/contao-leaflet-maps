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

use Netzmacht\Contao\Leaflet\Filter\Filter;

/**
 * Class AbstractTypeMapper is the base mapper for tables containing different types of definitins.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
abstract class AbstractTypeMapper extends AbstractMapper
{
    /**
     * The definition type.
     *
     * @var string
     */
    protected static $type;

    /**
     * {@inheritdoc}
     */
    public function match($model, Filter $filter = null)
    {
        return parent::match($model) && $model->type === static::$type;
    }
}
