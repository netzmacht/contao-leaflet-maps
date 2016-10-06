<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\DependencyInjection;

use Netzmacht\Contao\Leaflet\Boot;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class LeafletServices describes services provided by the leaflet package.
 *
 * @package Netzmacht\Contao\Leaflet\DependencyInjection
 */
class LeafletServices
{
    /**
     * Service name for the alias generator which creates valid js aliases.
     *
     * @return AliasGenerator
     */
    const ALIAS_GENERATOR = 'leaflet.alias-generator.default';

    /**
     * Service name of the boot handler.
     *
     * @return Boot
     */
    const BOOT = 'leaflet.boot';

    /**
     * Service name of the definition builder.
     *
     * @return Leaflet
     */
    const DEFINITION_BUILDER = 'leaflet.definition.builder';

    /**
     * Service name of the encoder factory used inside of the definition builder.
     *
     * @return \callable
     */
    const DEFINITION_ENCODER_FACTORY = 'leaflet.definition.builder.encoder-factory';

    /**
     * Service name of the internal used event dispatcher of the definition builder.
     *
     * @return EventDispatcherInterface
     */
    const DEFINITION_BUILDER_EVENT_DISPATCHER = 'leaflet.definition.builder.event-dispatcher';

    /**
     * Service name of the definition mapper.
     *
     * @return DefinitionMapper
     */
    const DEFINITION_MAPPER = 'leaflet.definition.mapper';

    /**
     * Service name of the leaflet map provider.
     *
     * @return MapProvider
     */
    const MAP_PROVIDER = 'leaflet.map.provider';

    /**
     * Service name of the map assets handler.
     *
     * @return Assets
     */
    const MAP_ASSETS = 'leaflet.map.assets';

    /**
     * Service name of the frontend value filter.
     *
     * @return ValueFilter
     */
    const FRONTEND_VALUE_FILTER = 'leaflet.frontend.value-filter';

    /**
     * Service name for the alias generator uses for rows being unique in the pid.
     *
     * @return AliasGenerator
     */
    const PARENT_ALIAS_GENERATOR = 'leaflet.alias-generator.parent';
}
