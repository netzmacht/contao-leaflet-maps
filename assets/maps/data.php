<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Netzmacht\Contao\Leaflet\Frontend\DataController;

define('TL_MODE', 'FE');
require(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/system/initialize.php');

$container  = $GLOBALS['container'];
$controller = new DataController($container['leaflet.map.service'], $container['input']);

$controller->execute();
