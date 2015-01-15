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

use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\Javascript\Encoder;
use Netzmacht\Javascript\Event\EncodeValueEvent;
use Netzmacht\Javascript\Event\GetReferenceEvent;
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
                array('encodeIcons', 1000),
                array('loadLayer', 100),
            ),
            GetReferenceEvent::NAME => array('referenceIcon', 100),
        );
    }

    public function referenceIcon(GetReferenceEvent $event)
    {
        $value = $event->getObject();

        if ($value instanceof Icon) {
            $event->setReference('L.Contao.getIcon(\'' . $value->getId() . '\')');
            $event->stopPropagation();
        }

    }

    /**
     * Force that icons are encoded as reference to the L.Contao icon registry.
     *
     * @param EncodeValueEvent $event The subscribed event.
     *
     * @return void
     */
    public function encodeIcons(EncodeValueEvent $event)
    {
        $value = $event->getValue();

        if ($value instanceof Icon) {
            //$event->addLine('L.Contao.getIcon(\'' . $value->getId() . '\')');
            $event->setSuccessful();
            $event->stopPropagation();
        }
    }

    public function loadLayer(EncodeValueEvent $event)
    {
        $value   = $event->getValue();
        $encoder = $event->getEncoder();
        $ref     = $encoder->encodeReference($value);

        if ($value instanceof OmnivoreLayer) {
            $url = $value->getUrl();

            if ($url instanceof RequestUrl) {
                $url = $url->getHash();
            }

            $event->addLine(
                sprintf(
                    '%s = L.Contao.loadLayer(%s, %s, %s, %s, map);',
                    $ref,
                    $encoder->encodeValue($url),
                    $encoder->encodeValue(strtolower(str_replace('Omnivore.', '', $value->getType()))),
                    $encoder->encodeValue($value->getOptions()),
                    $this->encodeCustomLayer($value, $encoder)
                )
            );

            foreach ($value->getLayers() as $layer) {
                $event->addLine(sprintf('%s.addLayer(%s);', $ref, $encoder->encodeReference($layer)));
            }

            foreach ($value->getMethodCalls() as $call) {
                $event->addLine($call->encode($encoder, $encoder->getOutput()));
            }
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
