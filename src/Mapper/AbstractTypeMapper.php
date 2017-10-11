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

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\Contao\Leaflet\Request\Request;

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
    public function match($model, Request $request = null)
    {
        return parent::match($model) && $model->type === static::$type;
    }
}
