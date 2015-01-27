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
 * Class PopupModel.
 *
 * @property mixed|null offset
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class PopupModel extends AbstractActiveModel
{
    /**
     * The table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_popup';
}
