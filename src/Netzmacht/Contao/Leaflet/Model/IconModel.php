<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Model;


/**
 * @property mixed|null iconImage
 * @property mixed|null iconAnchor
 * @property mixed|null popupAnchor
 * @property mixed|null iconRetinaImage
 * @property mixed|null shadowAnchor
 * @property mixed|null shadowRetinaImage
 * @property mixed|null shadowImage
 */
class IconModel extends \Model
{
    protected static $strTable = 'tl_leaflet_icon';
}
