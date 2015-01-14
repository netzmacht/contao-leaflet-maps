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

use Netzmacht\Javascript\Encoder;
use Netzmacht\Javascript\Event\BuildEvent;
use Netzmacht\Javascript\Event\EncodeValueEvent;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Definition\Type\Icon;
use Netzmacht\LeafletPHP\Plugins\Omnivore\OmnivoreLayer;
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
                array('endWrapper', -1000),
            ),
            EncodeValueEvent::NAME => array(
                array('encodeIcons', 100),
                array('loadLayer', 100),
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

    public function loadLayer(EncodeValueEvent $event)
    {
        $value   = $event->getValue();
        $encoder = $event->getEncoder();

        if ($event->getReferenced() < Encoder::REFERENCE_REQUIRED && $value instanceof OmnivoreLayer) {
            //$event->stopPropagation();
            $event->addLine(
                sprintf(
                    '%s = ContaoLeaflet.loadLayer(%s, %s, %s, %s, map.map);',
                    $encoder->encodeReference($value),
                    $encoder->encodeValue($value->getUrl()),
                    $encoder->encodeValue(strtolower(str_replace('Omnivore.', '', $value->getType()))),
                    $encoder->encodeValue($value->getOptions()),
                    $this->encodeCustomLayer($value, $encoder)
                )
            );
        }
    }

    /**
     * Encode a custom layer for the omnivore plugin.
     *
     * @param OmnivoreLayer $layer   The layer.
     * @param Encoder       $encoder The javascript encoder.
     *
     * @return string
     */
    protected function encodeCustomLayer(OmnivoreLayer $layer, Encoder $encoder)
    {
        $customLayer = $layer->getCustomLayer();

        if ($customLayer instanceof GeoJson && !$customLayer->getMethodCalls()) {
            return sprintf(
                'L.geoJson(null, %s)',
                $encoder->encodeValue($customLayer->getOptions())
            );
        } elseif ($customLayer) {
            return $encoder->encodeReference($customLayer);
        }

        return 'null';
    }
}
