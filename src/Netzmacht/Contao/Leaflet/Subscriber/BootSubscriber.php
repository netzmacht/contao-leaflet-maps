<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Subscriber;

use ContaoCommunityAlliance\Contao\EventDispatcher\EventDispatcherInitializer;
use Netzmacht\Contao\Leaflet\ContaoAssets;
use Netzmacht\Contao\Leaflet\DependencyInjection\LeafletServices;
use Netzmacht\Contao\Leaflet\Event\GetJavascriptEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeDefinitionMapperEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeEventDispatcherEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeLeafletBuilderEvent;
use Netzmacht\Contao\Leaflet\Frontend\InsertTag\LeafletInsertTagParser;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Mapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BootSubscriber provides handlers for leaflet boot process.
 *
 * @package Netzmacht\Contao\Leaflet\Subscriber
 */
class BootSubscriber implements EventSubscriberInterface
{
    /**
     * Leaflet mapper configuration.
     *
     * @var array
     */
    private $mappers;

    /**
     * Leaflet encoder configuration.
     *
     * @var array
     */
    private $encoders;

    /**
     * Leaflet libraries configuration.
     *
     * @var array
     */
    private $libraries;

    /**
     * Assets manager.
     *
     * @var ContaoAssets
     */
    private $assets;

    /**
     * Definition mapper.
     *
     * @var DefinitionMapper
     */
    private $definitionMapper;

    /**
     * BootSubscriber constructor.
     *
     * @param ContaoAssets $assets    Leaflet assets manager.
     * @param array        $mappers   Leaflet mapper configuration.
     * @param array        $encoders  Leaflet encoder configuration.
     * @param array        $libraries Leaflet libraries configuration.
     */
    public function __construct(
        ContaoAssets $assets,
        array $mappers,
        array $encoders,
        array $libraries
    ) {
        $this->assets    = $assets;
        $this->mappers   = $mappers;
        $this->encoders  = $encoders;
        $this->libraries = $libraries;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            InitializeSystemEvent::NAME           => 'initializeInsertTagParser',
            InitializeDefinitionMapperEvent::NAME => 'initializeDefinitionMapper',
            InitializeEventDispatcherEvent::NAME  => 'initializeEventDispatcher',
            InitializeLeafletBuilderEvent::NAME   => 'initializeLeafletBuilder',
            GetJavascriptEvent::NAME              => array(array('loadAssets'), array('loadIcons')),
        );
    }

    /**
     * Initialize the leaflet insert tag parser.
     *
     * @param InitializeSystemEvent $event The event.
     *
     * @return void
     */
    public function initializeInsertTagParser(InitializeSystemEvent $event)
    {
        $container   = $event->getContainer();
        $debugMode   = $container->get(Services::CONFIG)->get('debugMode');
        $mapProvider = $container->get(LeafletServices::MAP_PROVIDER);
        $parser      = new LeafletInsertTagParser($mapProvider, $debugMode);

        $container->get(Services::INSERT_TAG_REPLACER)->registerParser($parser);
    }

    /**
     * Create and register all configured mappers.
     *
     * @param InitializeDefinitionMapperEvent $event The subscribed event.
     *
     * @return void
     */
    public function initializeDefinitionMapper(InitializeDefinitionMapperEvent $event)
    {
        $mapper                 = $event->getDefinitionMapper();
        $this->definitionMapper = $mapper;

        foreach ($this->mappers as $className) {
            if (is_array($className)) {
                $mapper->register($this->createMapper($className[0]), $className[1]);
            } else {
                $mapper->register($this->createMapper($className));
            }
        }
    }

    /**
     * Register all leaflet encoders.
     *
     * @param InitializeEventDispatcherEvent $event The subscribed event.
     *
     * @return void
     */
    public function initializeEventDispatcher(InitializeEventDispatcherEvent $event)
    {
        $dispatcher  = $event->getEventDispatcher();
        $initializer = new EventDispatcherInitializer();

        $initializer->addSubscribers($dispatcher, $this->encoders);
    }

    /**
     * Register all libraries assets.
     *
     * @param InitializeLeafletBuilderEvent $event The subscribed event.
     *
     * @return void
     */
    public function initializeLeafletBuilder(InitializeLeafletBuilderEvent $event)
    {
        $builder = $event->getBuilder();

        foreach ($this->libraries as $name => $assets) {
            if (!empty($assets['css'])) {
                list ($source, $type) = (array) $assets['css'];
                $builder->registerStylesheet($name, $source, $type ?: Assets::TYPE_FILE);
            }

            if (!empty($assets['javascript'])) {
                list ($source, $type) = (array) $assets['javascript'];
                $builder->registerJavascript($name, $source, $type ?: Assets::TYPE_FILE);
            }
        }
    }

    /**
     * Load Contao leaflet assets.
     *
     * @return void
     */
    public function loadAssets()
    {
        $this->assets->addJavascript('assets/leaflet/maps/contao-leaflet.js', ContaoAssets::TYPE_FILE);
    }

    /**
     * Load icons.
     *
     * @return void
     */
    public function loadIcons()
    {
        if (!$this->definitionMapper) {
            return;
        }

        $collection = IconModel::findBy('active', true);

        if ($collection) {
            $buffer = '';
            $icons  = array();

            foreach ($collection as $model) {
                /** @var ImageIcon $icon */
                $icon    = $this->definitionMapper->handle($model);
                $icons[] = array(
                    'id'      => $icon->getId(),
                    'type'    => lcfirst($icon->getType()),
                    'options' => $icon->getOptions(),
                );
            }

            if ($icons) {
                $buffer = sprintf('L.contao.loadIcons(%s);', json_encode($icons));
            }

            $file = new \File('assets/leaflet/js/icons.js');
            $file->write($buffer);
            $file->close();

            // @codingStandardsIgnoreStart
            // TODO: Cache it.
            // codingStandardsIgnoreEnd
            $this->assets->addJavascript('assets/leaflet/js/icons.js', ContaoAssets::TYPE_FILE);
        }
    }

    /**
     * Create a new mapper.
     *
     * @param mixed $mapper The mapper class or callable factory.
     *
     * @return Mapper
     */
    private function createMapper($mapper)
    {
        if (is_callable($mapper)) {
            return $mapper();
        }

        return new $mapper;
    }
}
