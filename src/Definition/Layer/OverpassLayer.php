<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Definition\Layer;

use Netzmacht\JavascriptBuilder\Encoder;
use Netzmacht\JavascriptBuilder\Type\AnonymousFunction;
use Netzmacht\JavascriptBuilder\Type\ConvertsToJavascript;
use Netzmacht\JavascriptBuilder\Type\Expression;
use Netzmacht\LeafletPHP\Definition\AbstractLayer;
use Netzmacht\LeafletPHP\Definition\HasOptions;
use Netzmacht\LeafletPHP\Encoder\EncodeHelperTrait;

/**
 * Class OverpassLayer provides implementation of https://github.com/kartenkarsten/leaflet-layer-overpass.
 *
 * @package Netzmacht\LeafletPHP\Plugins\OverpassLayer
 */
class OverpassLayer extends AbstractLayer implements HasOptions, ConvertsToJavascript
{
    use EncodeHelperTrait;

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'OverpassLayer';
    }

    /**
     * {@inheritdoc}
     */
    public static function getRequiredLibraries()
    {
        $libs   = parent::getRequiredLibraries();
        $libs[] = 'osmtogeojson';

        return $libs;
    }

    /**
     * OverpassLayer constructor.
     *
     * @param string $identifier Indicator of the layer.
     * @param array  $options    Options.
     */
    public function __construct($identifier, array $options = [])
    {
        parent::__construct($identifier);

        $this->setOptions($options);
    }

    /**
     * Set the query.
     *
     * @param string $query Query.
     *
     * @return $this
     */
    public function setQuery($query)
    {
        return $this->setOption('query', $query);
    }

    /**
     * Get query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->getOption('query', '(node(BBOX)[organic];node(BBOX)[second_hand];);out qt;');
    }

    /**
     * Set the endpoint.
     *
     * @param string $endpoint Endpoint.
     *
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        return $this->setOption('endpoint', $endpoint);
    }

    /**
     * Get endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getOption('endpoint', '//overpass-api.de/api/');
    }

    /**
     * Set point to layer function.
     *
     * @param Expression|AnonymousFunction $function The function callback.
     *
     * @return $this
     */
    public function setPointToLayer($function)
    {
        return $this->setOption('pointToLayer', $function);
    }

    /**
     * Set on each feature function.
     *
     * @param Expression|AnonymousFunction $function The function callback.
     *
     * @return $this
     */
    public function setOnEachFeature($function)
    {
        return $this->setOption('onEachFeature', $function);
    }

    /**
     * Set the minZoom.
     *
     * @param int $minZoom MinZoom.
     *
     * @return $this
     */
    public function setMinZoom($minZoom)
    {
        return $this->setOption('minZoom', (int) $minZoom);
    }

    /**
     * Get minZoom.
     *
     * @return int
     */
    public function getMinZoom()
    {
        return $this->getOption('minZoom', 15);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(Encoder $encoder, $flags = null)
    {
        $buffer = sprintf(
            '%s = new L.OverPassLayer(%s)%s',
            $encoder->encodeReference($this),
            $encoder->encodeArray($this->getOptions(), JSON_FORCE_OBJECT),
            $encoder->close($flags)
        );

        $buffer .= $this->encodeMethodCalls($this->getMethodCalls(), $encoder, $flags);

        return $buffer;
    }
}
