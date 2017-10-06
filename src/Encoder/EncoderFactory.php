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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Encoder;

use Netzmacht\JavascriptBuilder\Encoder;
use Netzmacht\JavascriptBuilder\Encoder\ChainEncoder;
use Netzmacht\JavascriptBuilder\Output;
use Netzmacht\JavascriptBuilder\Symfony\EventDispatchingEncoder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EncoderFactory.
 *
 * @package Netzmacht\Contao\Leaflet\Encoder
 */
final class EncoderFactory
{
    /**
     * Definition builder event dispatcher.
     *
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * EncoderFactory constructor.
     *
     * @param EventDispatcher $dispatcher Definition builder event dispatcher.
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create the encoder.
     *
     * @param Output $output Output object.
     *
     * @return Encoder
     */
    public function __invoke(Output $output): Encoder
    {
        $encoder = (new ChainEncoder())
            ->register(new Encoder\MultipleObjectsEncoder())
            ->register(new EventDispatchingEncoder($this->dispatcher))
            ->register(new Encoder\JavascriptEncoder($output, JSON_UNESCAPED_SLASHES));

        return $encoder;
    }
}
