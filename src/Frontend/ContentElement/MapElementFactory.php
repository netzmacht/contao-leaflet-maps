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

namespace Netzmacht\Contao\Leaflet\Frontend\ContentElement;

use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Psr\Container\ContainerInterface as Container;

/**
 * Class MapElementFactory
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\ContentElement
 */
class MapElementFactory implements ComponentFactory
{
    /**
     * Dependency container.
     *
     * @var Container
     */
    private $container;

    /**
     * MapElementFactory constructor.
     *
     * @param Container $container Dependency container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($model): bool
    {
        return $model->type === 'leaflet';
    }

    /**
     * {@inheritDoc}
     */
    public function create($model, string $column): Component
    {
        return new MapElement(
            $model,
            $this->container->get('templating'),
            $this->container->get('translator'),
            $this->container->get('netzmacht.contao_leaflet.map.provider'),
            $this->container->get('netzmacht.contao_toolkit.contao.input_adapter'),
            $this->container->get('netzmacht.contao_toolkit.contao.config_adapter'),
            $column
        );
    }
}
