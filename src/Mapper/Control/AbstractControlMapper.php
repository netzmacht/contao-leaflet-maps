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

namespace Netzmacht\Contao\Leaflet\Mapper\Control;

use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Model\ControlModel;

/**
 * Class AbstractControlMapper is the base mapper for the control model.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class AbstractControlMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = ControlModel::class;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->optionsBuilder->addOption('position');
    }
}
