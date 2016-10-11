<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
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
