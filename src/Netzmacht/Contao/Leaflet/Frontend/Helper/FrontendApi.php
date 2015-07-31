<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend\Helper;

/**
 * Class FrontendApi provides access to the frontend api of contao.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\Helper
 */
class FrontendApi extends \Frontend
{
    /**
     * Call a Contao method no matter if it's protected.
     *
     * Inspired by Haste.
     *
     * @param string $method    The method name.
     * @param array  $arguments The arguments.
     *
     * @see https://github.com/codefog/contao-haste/commits/master/library/Haste/Haste.php
     *
     * @return mixed
     */
    public function call($method, array $arguments = array())
    {
        return call_user_func_array(array($this, $method), $arguments);
    }
}
