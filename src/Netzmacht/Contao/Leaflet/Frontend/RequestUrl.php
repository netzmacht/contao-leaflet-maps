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
     * Create the request url.
     *
     * It combines the params and creates an hash for it.
     *
     * @param int         $dataId The data object id.
     * @param string|null $type   Object type. If empty it assumes a layer.
     * @param string|null $format Data format. If empty it assumes geojson.
     *
     * @return RequestUrl
     */
    public static function create($dataId, $type = null, $format = null)
    {
        $params = array(
            'for'    => static::$for,
            'type'   => $type != 'layer' ? $type : null,
            'id'     => $dataId,
            'format' => $format != 'geojson' ? $format : null
        );

        $hash = base64_encode(implode(',', $params));
        $url  = \Config::get('websitePath') . '/' . \Frontend::addToUrl('leaflet=' . $hash, false);

        return new static($url, $hash);
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
