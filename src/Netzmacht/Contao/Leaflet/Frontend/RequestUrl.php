<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;

use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;

/**
 * Class RequestUrl creates the request url.
 *
 * @package Netzmacht\Contao\Leaflet\Request
 */
class RequestUrl
{
    const BASE = 'assets/leaflet/maps/data.php';

    private static $for;

    public static function setFor($for)
    {
        static::$for = $for;
    }

    /**
     * Create the request url.
     *
     * @param int         $dataId The data object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     *
     * @return string
     */
    public static function create($dataId, $type = null, $format = null)
    {
        $params = array(
            'for'    => static::$for,
            'type'   => $type != 'layer' ? $type : null,
            'id'     => $dataId,
            'format' => $format != 'geojson' ? $format: null
        );

        $param = rawurlencode(base64_encode(implode(',', $params)) . '==');

        return \Config::get('websitePath') . '/' . \Frontend::addToUrl('leaflet=' . $param, false);
    }
}
