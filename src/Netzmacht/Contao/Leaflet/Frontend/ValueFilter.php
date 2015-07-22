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

use Netzmacht\Contao\Leaflet\Frontend\Helper\InsertTagReplacer;

/**
 * Class ValueFilter is a service class which can be used to filter values before passing them to an definition object.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class ValueFilter
{
    /**
     * The frontend api of Contao.
     *
     * @var InsertTagReplacer
     */
    private $insertTagReplacer;

    /**
     * Construct.
     *
     * @param InsertTagReplacer $replacer The insert tag replacer.
     */
    public function __construct($replacer)
    {
        $this->insertTagReplacer = $replacer;
    }

    /**
     * Filter a value so it can be passed to the frontend.
     *
     * The idea behind this extra method is that we just have to change one place if anything else than the
     * insert tags has to be replaced.
     *
     * @param string $value The given value.
     *
     * @return string
     */
    public function filter($value)
    {
        return $this->insertTagReplacer->replace($value);
    }
}
