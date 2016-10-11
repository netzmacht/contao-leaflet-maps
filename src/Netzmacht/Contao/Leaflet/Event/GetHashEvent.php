<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetHashEvent is emitted then a hash for a data object with an unknown type is required.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class GetHashEvent extends Event
{
    const NAME = 'leaflet.get-hash';

    /**
     * The data.
     *
     * @var mixed
     */
    private $data;

    /**
     * The hash.
     *
     * @var string
     */
    private $hash;

    /**
     * Construct.
     *
     * @param mixed $data The data.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the hash.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set the generated hash.
     *
     * @param string $hash The generated hash.
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = (string) $hash;

        return $this;
    }
}
