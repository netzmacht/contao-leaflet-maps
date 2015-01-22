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
     * {@inheritdoc}
     */
    public function replaceInsertTags($strBuffer, $blnCache = true)
    {
        return parent::replaceInsertTags($strBuffer, $blnCache);
    }
}
