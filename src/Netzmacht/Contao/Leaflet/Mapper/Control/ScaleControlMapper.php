<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Control;

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
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Control\Scale';

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

        $this->addOptions('maxWidth', 'metric', 'imperial', 'updateWhenIdle');
    }
}
