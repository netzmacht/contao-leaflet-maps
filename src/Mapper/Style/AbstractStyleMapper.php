<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Style;

use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;

/**
 * Class AbstractStyleMapper is the base mapper for the style model.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Style
 */
abstract class AbstractStyleMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\StyleModel';
}
