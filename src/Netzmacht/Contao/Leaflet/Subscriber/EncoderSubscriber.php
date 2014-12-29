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
use Netzmacht\LeafletPHP\Definition\Map;
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
                array('startWrapper', 1000),
                array('addAttribution'),
                array('endWrapper', -1000)
            )
        );
    }

    /**
     * Add contao-leaflet attribution.
     *
     * @param BuildEvent $event
     */
    public function addAttribution(BuildEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Map) {
            $attribution = <<<HTML
map.map.attributionControl.addAttribution(
    '<a href="http://www.netzmacht.de/contao-leaflet">netzmacht <em>creative</em></a>'
);
HTML;
            $event->getOutput()->addLine($attribution);
        }
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
            $line = sprintf('window.ContaoLeaflet.addMap(\'%s\', (function() {', $object->getId());
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
}
