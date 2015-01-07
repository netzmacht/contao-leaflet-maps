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
use Netzmacht\Contao\Leaflet\Event\GetJavascriptEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeDefinitionMapperEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeEventDispatcherEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeLeafletBuilderEvent;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Javascript\Output;
use Netzmacht\LeafletPHP\Definition\Type\Icon;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BootSubscriber
 *
 * @package Netzmacht\Contao\Leaflet\Subscriber
 */
class BootSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            InitializeDefinitionMapperEvent::NAME => 'initializeDefinitionMapper',
            InitializeEventDispatcherEvent::NAME  => 'initializeEventDispatcher',
            InitializeLeafletBuilderEvent::NAME   => 'initializeLeafletBuilder',
            GetJavascriptEvent::NAME              => array(array('loadAssets'), array('loadIcons')),
        );
    }

    /**
     * Create and register all configured mappers.
     *
     * @param InitializeDefinitionMapperEvent $event The subscribed event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeDefinitionMapper(InitializeDefinitionMapperEvent $event)
    {
        $mapper = $event->getDefinitionMapper();

        foreach ($GLOBALS['LEAFLET_MAPPERS'] as $className) {
            if (is_array($className)) {
                $mapper->register(new $className[0], $className[1]);
            } else {
                $mapper->register(new $className());
            }
        }
    }

    /**
     * Register all leaflet encoders.
     *
     * @param InitializeEventDispatcherEvent $event The subscribed event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeEventDispatcher(InitializeEventDispatcherEvent $event)
    {
        $dispatcher  = $event->getEventDispatcher();
        $initializer = new EventDispatcherInitializer();

        $initializer->addSubscribers($dispatcher, $GLOBALS['LEAFLET_ENCODERS']);
    }

    /**
     * Register all libraries assets.
     *
     * @param InitializeLeafletBuilderEvent $event The subscribed event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initializeLeafletBuilder(InitializeLeafletBuilderEvent $event)
    {
        $builder = $event->getBuilder();

        foreach ($GLOBALS['LEAFLET_ASSETS'] as $name => $assets) {
            if (!empty($assets['css'])) {
                foreach ($assets['css'] as $javascript) {
                    $builder->registerStylesheet($name, $javascript[0], $javascript[1]);
                }
            }

            if (!empty($assets['javascript'])) {
                foreach ($assets['javascript'] as $javascript) {
                    $builder->registerJavascript($name, $javascript[0], $javascript[1]);
                }
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
        $GLOBALS['TL_JAVASCRIPT'][] = 'assets/leaflet/libs/contao/contao-leaflet.js|static';
    }

    /**
     * Load icons.
     *
     * @throws \Netzmacht\Javascript\Exception\EncodeValueFailed
     */
    public function loadIcons()
    {
        $collection = IconModel::findBy('active', true);

        if ($collection) {
            /** @var DefinitionMapper $mapper */
            $mapper = $GLOBALS['container']['leaflet.definition.mapper'];
            $buffer = '';
            $icons  = array();

            /** @var Leaflet $builder */
            $builder = $GLOBALS['container']['leaflet.definition.builder'];
            $encoder = $builder->getBuilder()->getEncoder();

            foreach ($collection as $model) {
                /** @var Icon $icon */
                $icon    = $mapper->handle($model);
                $icons[] = array(
                    'id'      => $icon->getId(),
                    'type'    => lcfirst($icon->getType()),
                    'options' => $icon->getOptions(),
                );
            }

            if ($icons) {
                $buffer = sprintf('ContaoLeaflet.loadIcons(%s);', $encoder->encodeValue($icons));
            }

            $file = new \File('assets/leaflet/js/icons.js');
            $file->write($buffer);
            $file->close();

            // TODO: Cache it.
            $GLOBALS['TL_JAVASCRIPT'][] = 'assets/leaflet/js/icons.js|static';
        }
    }
}
