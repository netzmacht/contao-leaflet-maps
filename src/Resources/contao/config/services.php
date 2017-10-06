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

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Leaflet\Alias\DefaultAliasFilter;
use Netzmacht\Contao\Leaflet\Boot;
use Netzmacht\Contao\Leaflet\ContaoAssets;
use Netzmacht\Contao\Leaflet\Listeners\Dca\ControlDcaListener;
use Netzmacht\Contao\Leaflet\Dca\FrontendIntegration;
use Netzmacht\Contao\Leaflet\Dca\LayerCallbacks;
use Netzmacht\Contao\Leaflet\Dca\LeafletCallbacks;
use Netzmacht\Contao\Leaflet\Listeners\Dca\MapDcaListener;
use Netzmacht\Contao\Leaflet\Dca\Validator;
use Netzmacht\Contao\Leaflet\Dca\VectorCallbacks;
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
// @codingStandardsIgnoreStart
global $container;
// @codingStandardsIgnoreEnd

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
        $container[LeafletServices::CACHE],
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
        $container[LeafletServices::MAP_ASSETS],
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

$container[LeafletServices::DEFINITION_BUILDER] = $container->share(function ($container) {
    /** @var Boot $boot */
    $boot       = $container[LeafletServices::BOOT];
    $dispatcher = $container[LeafletServices::DEFINITION_BUILDER_EVENT_DISPATCHER];
    $factory    = $container[LeafletServices::DEFINITION_ENCODER_FACTORY];

    $builder = new Builder($factory);
    $leaflet = new Leaflet($builder, $dispatcher, array(), (JSON_UNESCAPED_SLASHES ^ Flags::BUILD_STACK));

    return $boot->initializeLeafletBuilder($leaflet);
});

$container[LeafletServices::FRONTEND_VALUE_FILTER] = $container->share(function ($container) {
    return new ValueFilter($container[Services::INSERT_TAG_REPLACER]);
});

/*
 * Internal used leaflet cache.
 */

$container[LeafletServices::CACHE] = $container->share(
    function ($container) {
        if ($container[Services::PRODUCTION_MODE]) {
            return new FilesystemCache(TL_ROOT . '/system/cache/leaflet');
        } else {
            return new ArrayCache();
        }
    }
);

/*
 * Leaflet alias generator.
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

/*
 * Leaflet alias generator.
 * @return \Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator
 */

$container[LeafletServices::PARENT_ALIAS_GENERATOR] = $container->share(
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
                $aliasField,
                ['pid']
            );

            return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField, '_');
        };
    }
);

/*
 * Callback helper class for tl_leaflet_map.
 */

$container['leaflet.dca.map-callbacks'] = $container->share(
    function ($container) {
        return new MapDcaListener(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

/*
 * Callback helper class for tl_leaflet_layer.
 */

$container['leaflet.dca.layer-callbacks'] = $container->share(
    function ($container) {
        return new LayerCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION],
            $container[Services::TRANSLATOR],
            $GLOBALS['LEAFLET_LAYERS'],
            $GLOBALS['LEAFLET_TILE_PROVIDERS'],
            require TL_ROOT . '/system/modules/leaflet/config/leaflet_amenities.php'
        );
    }
);

/*
 * Callback helper class for tl_leaflet_control.
 */

$container['leaflet.dca.control-callbacks'] = $container->share(
    function ($container) {
        return new ControlDcaListener(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

/*
 * Callback helper class for tl_leaflet_control.
 */

$container['leaflet.dca.vector-callbacks'] = $container->share(
    function ($container) {
        return new VectorCallbacks($container[Services::DCA_MANAGER]);
    }
);

/*
 * Callback helper class for frontend integration.
 */

$container['leaflet.dca.frontend-integration'] = $container->share(
    function ($container) {
        return new FrontendIntegration(
            $container[Services::TRANSLATOR]
        );
    }
);

/*
 * Common callback helpers.
 */

$container['leaflet.dca.common'] = $container->share(
    function ($container) {
        return new LeafletCallbacks(
            $container[Services::FILE_SYSTEM]
        );
    }
);

/*
 * Validator helper class.
 */

$container['leaflet.dca.validator'] = $container->share(
    function ($container) {
        return new Validator(
            $container[Services::TRANSLATOR]
        );
    }
);

/*
 * Component factory for content element.
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

/*
 * Component factory for frontend module.
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
