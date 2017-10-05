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

namespace Netzmacht\Contao\Leaflet\Filter;

use Netzmacht\LeafletPHP\Value\LatLngBounds;

/**
 * The Bounds box filter.
 *
 * @package Netzmacht\Contao\Leaflet\Filter
 */
class BboxFilter implements Filter
{
    /**
     * The bounds.
     *
     * @var LatLngBounds
     */
    private $bounds;

    /**
     * Construct.
     *
     * @param LatLngBounds $bounds The bounds.
     */
    public function __construct(LatLngBounds $bounds)
    {

        $this->bounds = $bounds;
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'bbox';
    }

    /**
     * {@inheritdoc}
     */
    public static function fromRequest($request)
    {
        return new static(LatLngBounds::fromString($request));
    }

    /**
     * {@inheritdoc}
     */
    public function toRequest()
    {
        return $this->bounds->toString(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return array('bounds' => $this->bounds);
    }

    /**
     * Get the bounds.
     *
     * @return LatLngBounds
     */
    public function getBounds()
    {
        return $this->bounds;
    }
}
