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

namespace Netzmacht\Contao\Leaflet\Filter;

use Netzmacht\LeafletPHP\Value\LatLng;

/**
 * Class DistanceFilter filters by a coordinate the the distance from it.
 *
 * @package Netzmacht\Contao\Leaflet\Filter
 */
class DistanceFilter implements Filter
{
    /**
     * The center coordinates.
     *
     * @var \Netzmacht\LeafletPHP\Value\LatLng
     */
    private $center;

    /**
     * The radius in meters.
     *
     * @var int
     */
    private $radius;

    /**
     * Construct.
     *
     * @param LatLng $center The center coordinates.
     * @param int    $radius The radius in meters.
     */
    public function __construct(LatLng $center, $radius)
    {
        $this->center = $center;
        $this->radius = (int) $radius;
    }


    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'distance';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If the value could not be parsed.
     */
    public static function fromRequest($request)
    {
        $values = explode(',', $request, 3);

        if (count($values) !== 3) {
            throw new \InvalidArgumentException(sprintf('Invalid request value "%s"', $request));
        }

        return new static(
            new LatLng($values[0], $values[1]),
            $values[2]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toRequest()
    {
        return $this->center->toString(true) . ',' . $this->radius;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return array(
            'radius' => $this->radius,
            'center' => $this->center
        );
    }
}
