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

use Netzmacht\Contao\Toolkit\InsertTag\Replacer;

/**
 * Class ValueFilter is a service class which can be used to filter values before passing them to an definition object.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class ValueFilter
{
    /**
     * The insert tag replacer.
     *
     * @var Replacer
     */
    private $insertTagReplacer;

    /**
     * Construct.
     *
     * @param Replacer $replacer The insert tag replacer.
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
