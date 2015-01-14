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
use Netzmacht\Javascript\Event\EncodeValueEvent;
use Netzmacht\LeafletPHP\Definition\Group\GeoJson;
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
            EncodeValueEvent::NAME => array(
                array('encodeIcons', 100),
                array('loadLayer', 100),
            ),
        );
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

        if ($value instanceof OmnivoreLayer) {
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
