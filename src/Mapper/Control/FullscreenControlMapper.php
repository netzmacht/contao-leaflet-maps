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

use Netzmacht\LeafletPHP\Plugins\FullScreen\FullScreenControl;

/**
 * Class FullscreenControlMapper.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class FullscreenControlMapper extends AbstractControlMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = FullScreenControl::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'fullscreen';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addOption('forceSeparateButton', 'separate')
            ->addConditionalOption('title', 'title', 'buttonTitle')
            ->addOption('forcePseudoFullScreen', 'simulateFullScreen');
    }
}
