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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Bundle\DependencyInjection\Pass;

use Netzmacht\LeafletPHP\Assets;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterLibrariesPass.
 *
 * @package Netzmacht\Contao\Leaflet\DependencyInjection\Pass
 */
class RegisterLibrariesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_leaflet.definition.builder')) {
            return;
        }

        if (!$container->hasParameter('netzmacht.contao_leaflet.libraries')) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_leaflet.definition.builder');
        $libraries  = $container->getParameter('netzmacht.contao_leaflet.libraries');

        foreach ($libraries as $name => $assets) {
            if (!empty($assets['css'])) {
                list ($source, $type) = (array) $assets['css'];
                $definition->addMethodCall('registerStylesheet', [$name, $source, $type ?: Assets::TYPE_FILE]);
            }

            if (!empty($assets['javascript'])) {
                list ($source, $type) = (array) $assets['javascript'];
                $definition->addMethodCall('registerJavascript', [$name, $source, $type ?: Assets::TYPE_FILE]);
            }
        }
    }
}
