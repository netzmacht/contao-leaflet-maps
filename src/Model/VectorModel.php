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

namespace Netzmacht\Contao\Leaflet\Model;

/**
 * Class VectorModel for the tl_leaflet_vector table.
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class VectorModel extends AbstractActiveModel
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_vector';
}
