<?php

use Netzmacht\Contao\Leaflet\Controller\DataController;

define('TL_MODE', 'FE');
require(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/system/initialize.php');

$container  = $GLOBALS['container'];
$controller = new DataController($container['leaflet.map.service'], $container['input']);

$controller->execute();
