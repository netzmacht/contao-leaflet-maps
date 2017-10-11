<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Definition;

use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Vector\Path;

/**
 * Interface Style describes a style definition.
 *
 * @package Netzmacht\Contao\Leaflet\Definition
 */
interface Style extends Definition
{
    /**
     * Apply style to a given vector.
     *
     * @param Path $vector The vector path.
     *
     * @return $this
     */
    public function apply(Path $vector);
}
