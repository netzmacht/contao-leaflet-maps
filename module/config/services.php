<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Netzmacht\Contao\Leaflet\Boot;
use Netzmacht\Contao\Leaflet\Frontend\Helper\FrontendApi;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\MapService;
use Netzmacht\Contao\Leaflet\ServiceContainer;
use Netzmacht\JavascriptBuilder\Builder;
use Netzmacht\JavascriptBuilder\Encoder;
use Netzmacht\JavascriptBuilder\Encoder\Chain\MultipleChain;
use Netzmacht\JavascriptBuilder\Encoder\ChainEncoder;
use Netzmacht\JavascriptBuilder\Encoder\JavascriptEncoder;
use Netzmacht\JavascriptBuilder\Encoder\MultipleObjectsEncoder;
use Netzmacht\JavascriptBuilder\Flags;
use Netzmacht\JavascriptBuilder\Output;
use Netzmacht\JavascriptBuilder\Symfony\EventDispatchingEncoder;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventDispatcher;

/** @var \Pimple $container */
global $container;

/*
 * Leaflet map service is a simply api entry to to get the leaflet map from the database.
 */
$container['leaflet.map.service'] = $container->share(function ($container) {
    return new MapService(
        $container['leaflet.definition.mapper'],
        $container['leaflet.definition.builder'],
        $container['event-dispatcher'],
        $container['input']
    );
});


/*
 * The leaflet boot.
 */
$container['leaflet.boot'] = $container->share(function ($container) {
    return new Boot($container['event-dispatcher']);
});


/*
 * The definition mapper.
 */
$container['leaflet.definition.mapper'] = $container->share(function ($container) {
    /** @var Boot $boot */
    $boot   = $container['leaflet.boot'];
    $mapper = new DefinitionMapper($container['event-dispatcher']);

    return $boot->initializeDefinitionMapper($mapper);
});


/*
 * The local event dispatcher is used for the leaflet javascript encoding system.
 */
$container['leaflet.definition.builder.event-dispatcher'] = $container->share(function ($container) {
    /** @var Boot $boot */
    $boot       = $container['leaflet.boot'];
    $dispatcher = new EventDispatcher();

    return $boot->initializeEventDispatcher($dispatcher);
});

/*
 * The javascript encoder factory being used for building the map javascript.
 */
$container['leaflet.definition.builder.encoder-factory'] = function ($container) {
    $dispatcher = $container['leaflet.definition.builder.event-dispatcher'];

    return function (Output $output) use ($dispatcher) {
        $encoder = new ChainEncoder();
        $encoder
            ->register(new MultipleObjectsEncoder())
            ->register(new EventDispatchingEncoder($dispatcher))
            ->register(new JavascriptEncoder($output, JSON_UNESCAPED_SLASHES));

        return $encoder;
    };
};

/*
 * The leaflet builder transforms the definition to javascript.
 */
$container['leaflet.definition.builder'] = $container->share(function($container) {
    /** @var Boot $boot */
    $boot       = $container['leaflet.boot'];
    $dispatcher = $container['leaflet.definition.builder.event-dispatcher'];
    $factory    = $container['leaflet.definition.builder.encoder-factory'];

    $builder = new Builder($factory);
    $leaflet = new Leaflet($builder, $dispatcher, array(), JSON_UNESCAPED_SLASHES ^ Flags::BUILD_STACK);

    return $boot->initializeLeafletBuilder($leaflet);
});

$container['leaflet.frontend.value-filter'] = $container->share(function() {
    return new ValueFilter(new FrontendApi());
});

$container['leaflet.service-container'] = $container->share(function($container) {
    return new ServiceContainer($container);
});
