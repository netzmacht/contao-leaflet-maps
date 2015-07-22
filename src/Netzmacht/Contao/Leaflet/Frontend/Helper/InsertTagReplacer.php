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
 * This class is a helper to replace insert tags.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\Helper
 */
class InsertTagReplacer
{
    /**
     * Replace insert tags with their values.
     *
     * @param string  $buffer The text with the tags to be replaced.
     * @param boolean $cache  If false, non-cacheable tags will be replaced.
     *
     * @return string
     */
    public function replace($buffer, $cache = true)
    {
        if (class_exists('InsertTags')) {
            $replacer = new \InsertTags();
            return $replacer->replace($buffer, $cache);
        }

        $frontendApi = new FrontendApi();

        return $frontendApi->replaceInsertTags($buffer, $cache);
    }
}
