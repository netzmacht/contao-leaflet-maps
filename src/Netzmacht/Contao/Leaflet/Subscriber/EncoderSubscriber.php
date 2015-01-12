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

use Netzmacht\Javascript\Event\BuildEvent;
use Netzmacht\Javascript\Event\EncodeValueEvent;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Definition\Type\Icon;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EncoderSubscriber subscribes to the internal encoding event dispatcher.
 *
 * @package Netzmacht\Contao\Leaflet\Subscriber
 */
class EncoderSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            BuildEvent::NAME => array(
                array('startWrapper', 100),
                array('endWrapper', -100),
            ),
            EncodeValueEvent::NAME => array(
                array('encodeIcons', 100)
            ),
        );
    }

    /**
     * Start the wrapper.
     *
     * The encoded map is wrapped so that it is added to window.ContaoLeaflet. You can subscribe the
     * "mapadded" event on window.ContaoLeaflet if you can to do some customize stuff.
     *
     * @param BuildEvent $event The subscribed event.
     *
     * @return void
     */
    public function startWrapper(BuildEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Map) {
            $line = sprintf('ContaoLeaflet.addMap(\'%s\', (function() {', $object->getId());
            $event->getOutput()->addLine($line);
        }
    }

    /**
     * End the wrapper.
     *
     * @param BuildEvent $event The subscribed event.
     *
     * @return void
     */
    public function endWrapper(BuildEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Map) {
            $line = 'return map; })());';
            $event->getOutput()->addLine($line);
        }
    }

    /**
     * Force that icons are encoded as reference to the ContaoLeaflet icon registry.
     *
     * @param EncodeValueEvent $event The subscribed event.
     *
     * @return void
     */
    public function encodeIcons(EncodeValueEvent $event)
    {
        $value = $event->getValue();

        if ($value instanceof Icon) {
            $event->addLine('ContaoLeaflet.getIcon(\'' . $value->getId() . '\')');
            $event->stopPropagation();
        }
    }
}
