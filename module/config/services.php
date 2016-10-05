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
use Netzmacht\Contao\Leaflet\Alias\UnderscoreFilter;
use Netzmacht\Contao\Leaflet\Boot;
use Netzmacht\Contao\Leaflet\ContaoAssets;
use Netzmacht\Contao\Leaflet\Frontend\MapElement;
use Netzmacht\Contao\Leaflet\Frontend\MapModule;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\MapProvider;
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
$container['leaflet.map.provider'] = $container->share(function ($container) {
    return new MapProvider(
        $container['leaflet.definition.mapper'],
        $container['leaflet.definition.builder'],
        $container['event-dispatcher'],
        $container['input'],
        $container['leaflet.map.assets']
    );
});

/*
 * Contao assets handler. Loads Leaflet assets as contao (static) assets.
 */
$container['leaflet.map.assets'] = $container->share(function () {
    return new ContaoAssets();
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

$container['leaflet.frontend.value-filter'] = $container->share(function($container) {
    return new ValueFilter($container[Services::INSERT_TAG_REPLACER]);
});

/**
 * Leaflet alias generator.
 *
 * @return \Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator
 */
$container['leaflet.alias-generator'] = $container->share(
    function ($container) {
        return function ($dataContainerName, $aliasField, $fields) use ($container) {
            $filters = [
                new ExistingAliasFilter(),
                new SlugifyFilter($fields),
                new SuffixFilter(),
                new UnderscoreFilter(false)
            ];

            $validator = new UniqueDatabaseValueValidator(
                $container[Services::DATABASE_CONNECTION],
                $dataContainerName,
                $aliasField
            );

            return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField);
        };
    }
);

$container['leaflet.dca.map-callbacks'] = $container->share(
    function ($container) {
        return new \Netzmacht\Contao\Leaflet\Dca\MapCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

$container['leaflet.dca.layer-callbacks'] = $container->share(
    function ($container) {
        return new \Netzmacht\Contao\Leaflet\Dca\LayerCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION],
            $GLOBALS['LEAFLET_LAYERS']
        );
    }
);

$container['leaflet.dca.control-callbacks'] = $container->share(
    function ($container) {
        return new \Netzmacht\Contao\Leaflet\Dca\ControlCallbacks(
            $container[Services::DCA_MANAGER],
            $container[Services::DATABASE_CONNECTION]
        );
    }
);

$container[Services::CONTENT_ELEMENTS_MAP]['leaflet'] = function ($model, $column, ContainerInterface $container) {
    return new MapElement(
        $model,
        $container->get(Services::TEMPLATE_FACTORY),
        $container->get(Services::TRANSLATOR),
        $container->get('leaflet.map.provider'),
        $container->get(Services::INPUT),
        $container->get(Services::CONFIG),
        $column
    );
};

$container[Services::MODULES_MAP]['leaflet'] = function ($model, $column, ContainerInterface $container) {
    return new MapModule(
        $model,
        $container->get(Services::TEMPLATE_FACTORY),
        $container->get(Services::TRANSLATOR),
        $container->get('leaflet.map.provider'),
        $container->get(Services::INPUT),
        $container->get(Services::CONFIG),
        $column
    );
};
