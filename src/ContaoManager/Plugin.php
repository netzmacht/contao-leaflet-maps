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

namespace Netzmacht\Contao\Leaflet\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Netzmacht\Contao\Leaflet\NetzmachtContaoLeafletBundle;
use Netzmacht\Contao\Toolkit\NetzmachtContaoToolkitBundle;

/**
 * Contao manager plugin.
 *
 * @package Netzmacht\Contao\Leaflet\ContaoManager
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(NetzmachtContaoLeafletBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NetzmachtContaoToolkitBundle::class])
                ->setReplace(['leaflet'])
        ];
    }
}