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

namespace Netzmacht\Contao\Leaflet\Bundle;

use Netzmacht\Contao\Leaflet\Bundle\DependencyInjection\Pass\RegisterLibrariesPass;
use Netzmacht\Contao\Leaflet\Bundle\DependencyInjection\Pass\RegisterDefinitionMapperPass;
use Netzmacht\Contao\Leaflet\Bundle\DependencyInjection\Pass\RegisterEncodersPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetzmachtContaoLeafletBundle.
 *
 * @package Netzmacht\Contao\Leaflet
 */
class NetzmachtContaoLeafletBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterDefinitionMapperPass());
        $container->addCompilerPass(new RegisterEncodersPass());
        $container->addCompilerPass(new RegisterLibrariesPass());
        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_leaflet_maps.layer_label_renderer',
                'netzmacht.contao_leaflet_maps.layer_label_renderer'
            )
        );
    }
}
