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

namespace Netzmacht\Contao\Leaflet\Model;

/**
 * IconModel class for the tl_leaflet_icon table.
 *
 * @property mixed|null iconImage
 * @property mixed|null iconAnchor
 * @property mixed|null popupAnchor
 * @property mixed|null iconRetinaImage
 * @property mixed|null shadowAnchor
 * @property mixed|null shadowRetinaImage
 * @property mixed|null shadowImage
 */
class IconModel extends AbstractActiveModel
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_icon';
}
