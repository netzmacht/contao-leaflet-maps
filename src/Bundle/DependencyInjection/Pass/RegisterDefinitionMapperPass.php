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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Bundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register all definition mappers.
 *
 * @package Netzmacht\Contao\Leaflet\DependencyInjection\Pass
 */
class RegisterDefinitionMapperPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_leaflet_maps.definition.mapper')) {
            return;
        }

        $definition     = $container->getDefinition('netzmacht.contao_leaflet_maps.definition.mapper');
        $taggedServices = $container->findTaggedServiceIds('netzmacht.contao_leaflet_maps.mapper');

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (isset($attributes['priority'])) {
                    $definition->addMethodCall('register', [new Reference($serviceId), (int) $attributes['priority']]);
                } else {
                    $definition->addMethodCall('register', [new Reference($serviceId)]);
                }
            }
        }
    }
}
