<?php

use Netzmacht\Contao\Leaflet\Controller\GeoJsonController;

define('TL_MODE', 'FE');
require(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/initialize.php');

$container  = $GLOBALS['container'];
$controller = new GeoJsonController($container['leaflet.map.service'], $container['input']);

echo $controller->execute();
