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
        return self::createBuilder($dataId, $type, $format)->getUrl();
    }

    /**
     * Create the request builder.
     *
     * @param int         $dataId The data object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     *
     * @return UrlBuilder
     */
    public static function createBuilder($dataId, $type = null, $format = null)
    {
        $path    = \Config::get('websitePath') . '/' . static::BASE;
        $builder = new UrlBuilder();
        $builder
            ->setPath($path)
            ->setQueryParameter('type', $type ?: 'layer')
            ->setQueryParameter('format', $format ?: 'geojson')
            ->setQueryParameter('id', $dataId)
            ->setQueryParameter('page', rawurlencode(base64_encode(self::getRequestUri())));

        return $builder;
    }

    /**
     * Get the request uri without leading slash.
     *
     * @return mixed
     */
    protected static function getRequestUri()
    {
        $uri = \Environment::get('requestUri');

        if (strpos($uri, '/') === 0) {
            return substr($uri, 1);
        }

        return $uri;
    }
}
