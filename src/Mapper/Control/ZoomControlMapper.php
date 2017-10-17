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

use Netzmacht\LeafletPHP\Definition\Control\Zoom;

/**
 * Class ZoomControlMapper maps the zoom database definition to the zoom control.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class ZoomControlMapper extends AbstractControlMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = Zoom::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'zoom';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addConditionalOption('zoomInText')
            ->addConditionalOption('zoomOutText')
            ->addConditionalOption('zoomInTitle')
            ->addConditionalOption('zoomOutTitle');
    }
}
