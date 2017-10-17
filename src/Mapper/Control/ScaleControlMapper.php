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

use Netzmacht\LeafletPHP\Definition\Control\Scale;

/**
 * Class ScaleControlMapper maps the database item of the type "scale" to the scale control.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class ScaleControlMapper extends AbstractControlMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = Scale::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'scale';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addOptions('maxWidth', 'metric', 'imperial', 'updateWhenIdle');
    }
}
