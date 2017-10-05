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

namespace Netzmacht\Contao\Leaflet\Frontend;

use Netzmacht\Contao\Leaflet\Filter\Filter;

/**
 * Class RequestUrl creates the request url.
 *
 * @package Netzmacht\Contao\Leaflet\Request
 */
class RequestUrl implements \JsonSerializable
{
    /**
     * The for param is the identifier to the responsible frontend module or content element.
     *
     * @var string
     */
    private static $for;

    /**
     * The leaflet hash.
     *
     * @var string
     */
    private $hash;

    /**
     * The request url as url path.
     *
     * @var string
     */
    private $url;

    /**
     * Request filter.
     *
     * @var Filter|null
     */
    private $filter;

    /**
     * Create the request url.
     *
     * It combines the params and creates an hash for it.
     *
     * @param int         $dataId The data object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     * @param Filter      $filter Optional filter.
     *
     * @return RequestUrl
     */
    public static function create($dataId, $type = null, $format = null, Filter $filter = null)
    {
        $params = array(
            'for'    => static::$for,
            'type'   => $type != 'layer' ? $type : null,
            'id'     => $dataId,
            'format' => $format != 'geojson' ? $format : null
        );

        $hash  = base64_encode(implode(',', $params));
        $query = 'leaflet=' . $hash;

        if ($filter) {
            $query .= '&amp;f=' . $filter->getName() . '&amp;v=' . $filter->toRequest();
        }

        $url = \Config::get('websitePath') . '/' . \Frontend::addToUrl($query, false);

        return new static($url, $hash, $filter);
    }

    /**
     * Set the for param.
     *
     * @param string $for The identifier which has the responsibility listen to the request.
     *
     * @return void
     */
    public static function setFor($for)
    {
        static::$for = $for;
    }

    /**
     * Construct.
     *
     * @param string $url  The request url.
     * @param string $hash The leaflet hash.
     */
    public function __construct($url, $hash)
    {
        $this->url  = $url;
        $this->hash = $hash;
    }

    /**
     * Get the leaflet url hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Get the whole url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get request filter.
     *
     * @return Filter|null
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Convert to string will always return the whole url.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->getUrl();
    }
}
