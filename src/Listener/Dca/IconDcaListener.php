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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

/**
 * Class IconDcaListener.
 *
 * @package Netzmacht\Contao\Leaflet\Listener\Dca
 */
class IconDcaListener
{
    /**
     * Icon type options.
     *
     * @var array
     */
    private $icons;

    /**
     * IconDcaListener constructor.
     *
     * @param array $icons Icon type options.
     */
    public function __construct(array $icons)
    {
        $this->icons = $icons;
    }

    /**
     * Get icon options.
     *
     * @return array
     */
    public function getIconOptions(): array
    {
        return $this->icons;
    }
}
