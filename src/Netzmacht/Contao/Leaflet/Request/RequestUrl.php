<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Request;

use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;

/**
 * Class RequestUrl creates the request url.
 *
 * @package Netzmacht\Contao\Leaflet\Request
 */
class RequestUrl
{
    const BASE = 'assets/leaflet/data.php';

    /**
     * Create the request url.
     *
     * @param int         $id     Object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     *
     * @return string
     */
    public static function create($id, $type = null, $format = null)
    {
        return self::createBuilder($id, $type, $format)->getUrl();
    }

    /**
     * Create the request builder.
     *
     * @param int         $id     Object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     *
     * @return UrlBuilder
     */
    public static function createBuilder($id, $type = null, $format = null)
    {
        $path    = \Config::get('websitePath') . '/' . static::BASE;
        $builder = new UrlBuilder();
        $builder
            ->setPath($path)
            ->setQueryParameter('type', $type ?: 'layer')
            ->setQueryParameter('format', $format ?: 'geojson')
            ->setQueryParameter('id', $id);

        return $builder;
    }
}
