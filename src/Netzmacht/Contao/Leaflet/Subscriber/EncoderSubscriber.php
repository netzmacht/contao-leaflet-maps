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
use Netzmacht\Javascript\Exception\EncodeValueFailed;
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

    /**
     * Create icon reference to the contao leaflet icon registry.
     *
     * @param GetReferenceEvent $event The subscribed event.
     *
     * @return void
     */
    public function referenceIcon(GetReferenceEvent $event)
    {
        $value = $event->getObject();

        if ($value instanceof Icon) {
            $event->setReference('L.contao.getIcon(\'' . $value->getId() . '\')');
            $event->stopPropagation();
        }
    }

    /**
     * Force that icons are encoded as reference to the L.contao icon registry.
     *
     * @param EncodeValueEvent $event The subscribed event.
     *
     * @return void
     */
    public function encodeIcons(EncodeValueEvent $event)
    {
        $value = $event->getValue();

        if ($value instanceof Icon) {
            // Do not encode the icon, as it is generated in an separate icon file.
            $event->setSuccessful();
            $event->stopPropagation();
        }
    }

    /**
     * Encode OmnivoreLayers so that the internal used contao.loadLayer method is used.
     *
     * @param EncodeValueEvent $event The subscribed event.
     *
     * @return void
     * @throws EncodeValueFailed If encoding failed.
     */
    public function loadLayer(EncodeValueEvent $event)
    {
        $value   = $event->getValue();
        $encoder = $event->getEncoder();
        $ref     = $encoder->encodeReference($value);

        if ($value instanceof OmnivoreLayer) {
            $url = $value->getUrl();

            if ($url instanceof RequestUrl) {
                $url = $url->getHash();
            } elseif (strpos($url, '/') !== false) {
                // Slash found, not contao leaflet hash, do not replace encoding.
                return;
            }

            $event->addLine(
                sprintf(
                    '%s = L.contao.loadLayer(%s, %s, %s, %s, map);',
                    $ref,
                    $encoder->encodeValue($url),
                    $encoder->encodeValue(strtolower(str_replace('Omnivore.', '', $value->getType()))),
                    $encoder->encodeArray($value->getOptions(), JSON_FORCE_OBJECT),
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
