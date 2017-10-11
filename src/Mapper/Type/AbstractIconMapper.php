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

namespace Netzmacht\Contao\Leaflet\Mapper\Type;

use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;

/**
 * Class AbstractIconMapper is the base mapper for the icon model.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Type
 */
class AbstractIconMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = IconModel::class;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->optionsBuilder->addConditionalOption('className');
    }
}
