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

use Netzmacht\LeafletPHP\Definition;

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
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Plugins\FullScreen\FullScreenControl';

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

        $this
            ->addOption('forceSeparateButton', 'separate')
            ->addConditionalOption('title', 'title', 'buttonTitle')
            ->addOption('forcePseudoFullScreen', 'simulateFullScreen');
    }
}
