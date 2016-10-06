<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Leaflet\Alias\DefaultAliasFilter;
use Netzmacht\Contao\Leaflet\Boot;
use Netzmacht\Contao\Leaflet\ContaoAssets;
use Netzmacht\Contao\Leaflet\Dca\ControlCallbacks;
use Netzmacht\Contao\Leaflet\Dca\FrontendIntegration;
use Netzmacht\Contao\Leaflet\Dca\LayerCallbacks;
use Netzmacht\Contao\Leaflet\Dca\MapCallbacks;
use Netzmacht\Contao\Leaflet\DependencyInjection\LeafletServices;
use Netzmacht\Contao\Leaflet\Frontend\MapElement;
use Netzmacht\Contao\Leaflet\Frontend\MapModule;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Leaflet\Subscriber\BootSubscriber;
use Netzmacht\Contao\Leaflet\Subscriber\GeoJsonSubscriber;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\JavascriptBuilder\Builder;
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
 * Leaflet map provider is a simply api entry to to get the leaflet map from the database.
 */
$container[LeafletServices::MAP_PROVIDER] = $container->share(function ($container) {
    return new MapProvider(
        $container[LeafletServices::DEFINITION_MAPPER],
        $container[LeafletServices::DEFINITION_BUILDER],
        $container[Services::EVENT_DISPATCHER],
        $container[Services::INPUT],
        $container[LeafletServices::MAP_ASSETS],
        $GLOBALS['LEAFLET_FILTERS'],
        \Config::get('debugMode') || \Config::get('displayErrors')
    );
});

/*
 * Contao assets handler. Loads Leaflet assets as contao (static) assets.
 */
$container[LeafletServices::MAP_ASSETS] = $container->share(function ($container) {
    return new ContaoAssets($container[Services::ASSETS_MANAGER]);
});

/*
 * The leaflet boot.
 */
$container[LeafletServices::BOOT] = $container->share(function ($container) {
    return new Boot($container[Services::EVENT_DISPATCHER]);
});

$container['leaflet.boot.subscriber'] = $container->share(function ($container) {
    return new BootSubscriber(
        $container[Services::ASSETS_MANAGER],
        $GLOBALS['LEAFLET_MAPPERS'],
        $GLOBALS['LEAFLET_ENCODERS'],
        $GLOBALS['LEAFLET_LIBRARIES']
    );
});


/*
 * The definition mapper.
 */
$container[LeafletServices::DEFINITION_MAPPER] = $container->share(function ($container) {
    /** @var Boot $boot */
    $boot   = $container[LeafletServices::BOOT];
    $mapper = new DefinitionMapper($container[Services::EVENT_DISPATCHER]);

    return $boot->initializeDefinitionMapper($mapper);
});


/*
 * The local event dispatcher is used for the leaflet javascript encoding system.
 */
$container[LeafletServices::DEFINITION_BUILDER_EVENT_DISPATCHER] = $container->share(function ($container) {
    /** @var Boot $boot */
    $boot       = $container[LeafletServices::BOOT];
    $dispatcher = new EventDispatcher();

    return $boot->initializeEventDispatcher($dispatcher);
});

/*
 * The javascript encoder factory being used for building the map javascript.
 */
$container[LeafletServices::DEFINITION_ENCODER_FACTORY] = function ($container) {
    $dispatcher = $container[LeafletServices::DEFINITION_BUILDER_EVENT_DISPATCHER];

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
$container[LeafletServices::DEFINITION_BUILDER] = $container->share(function($container) {
    /** @var Boot $boot */
    $boot       = $container[LeafletServices::BOOT];
    $dispatcher = $container[LeafletServices::DEFINITION_BUILDER_EVENT_DISPATCHER];
    $factory    = $container[LeafletServices::DEFINITION_ENCODER_FACTORY];

    $builder = new Builder($factory);
    $leaflet = new Leaflet($builder, $dispatcher, array(), JSON_UNESCAPED_SLASHES ^ Flags::BUILD_STACK);

    return $boot->initializeLeafletBuilder($leaflet);
});

$container[LeafletServices::FRONTEND_VALUE_FILTER] = $container->share(function($container) {
    return new ValueFilter($container[Services::INSERT_TAG_REPLACER]);
});

/**
 * Leaflet alias generator.
 *
 * @return \Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator
 */
$container[LeafletServices::ALIAS_GENERATOR] = $container->share(
    function ($container) {
        return function ($dataContainerName, $aliasField, $fields) use ($container) {
            $filters = [
                new ExistingAliasFilter(),
                new SlugifyFilter($fields),
                new DefaultAliasFilter($dataContainerName),
                new SuffixFilter(),
            ];

            $validator = new UniqueDatabaseValueValidator(
                $container[Services::DATABASE_CONNECTION],
                $dataContainerName,
                $aliasField
            );

            return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField, '_');
        };
    }
);

/**
 * Callback helper class for tl_leaflet_map.
 *
 * @return MapCallbacks
 */
$container['leaflet.dca.map-callbacks'] = $container->share(
    function ($container) {
        return new MapCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

/**
 * Callback helper class for tl_leaflet_layer.
 *
 * @return LayerCallbacks
 */
$container['leaflet.dca.layer-callbacks'] = $container->share(
    function ($container) {
        return new LayerCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION],
            $container[Services::TRANSLATOR],
            $GLOBALS['LEAFLET_LAYERS'],
            $GLOBALS['LEAFLET_TILE_PROVIDERS']
        );
    }
);

/**
 * Callback helper class for tl_leaflet_control.
 *
 * @return ControlCallbacks
 */
$container['leaflet.dca.control-callbacks'] = $container->share(
    function ($container) {
        return new ControlCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

/**
 * Callback helper class for frontend integration.
 *
 * @return FrontendIntegration
 */
$container['leaflet.dca.frontend-integration'] = $container->share(
    function ($container) {
        return new FrontendIntegration(
            $container[Services::TRANSLATOR]
        );
    }
);

/**
 * Component factory for content element.
 *
 * @param ContentModel       $model     Content model.
 * @param string             $column    Template section.
 * @param ContainerInterface $container Container.
 *
 * @return MapElement
 */
$container[Services::CONTENT_ELEMENTS_MAP]['leaflet'] = function ($model, $column, ContainerInterface $container) {
    return new MapElement(
        $model,
        $container->get(Services::TEMPLATE_FACTORY),
        $container->get(Services::TRANSLATOR),
        $container->get(LeafletServices::MAP_PROVIDER),
        $container->get(Services::INPUT),
        $container->get(Services::CONFIG),
        $column
    );
};

/**
 * Component factory for frontend module.
 *
 * @param ModuleModel        $model     Module model.
 * @param string             $column    Template section.
 * @param ContainerInterface $container Container.
 *
 * @return MapModule
 */
$container[Services::MODULES_MAP]['leaflet'] = function ($model, $column, ContainerInterface $container) {
    return new MapModule(
        $model,
        $container->get(Services::TEMPLATE_FACTORY),
        $container->get(Services::TRANSLATOR),
        $container->get(LeafletServices::MAP_PROVIDER),
        $container->get(Services::INPUT),
        $container->get(Services::CONFIG),
        $column
    );
};

$container['leaflet.subscriber.geo-json'] = $container->share(function () {
    return new GeoJsonSubscriber(
        $GLOBALS['LEAFLET_FEATURE_MODEL_PROPERTIES']
    );
});
