<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Subscriber;

use Netzmacht\Contao\Leaflet\Dca\Vector;
use Netzmacht\Contao\Leaflet\Event\ConvertToGeoJsonEvent;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MarkerModel;
use Netzmacht\Contao\Leaflet\Model\VectorModel;
use Netzmacht\LeafletPHP\Definition\GeoJson\Feature;
use Netzmacht\LeafletPHP\Definition\HasPopup;
use Netzmacht\LeafletPHP\Definition\UI\Marker;
use Netzmacht\LeafletPHP\Definition\Vector\Circle;
use Netzmacht\LeafletPHP\Definition\Vector\CircleMarker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeoJsonSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConvertToGeoJsonEvent::NAME => array(
                array('addPopup'),
                array('enrichMarker'),
                array('enrichVector'),
                array('enrichCircle')
            )
        );
    }

    /**
     * Add popup property for definitions with an popup.
     *
     * @param ConvertToGeoJsonEvent $event The subscribed event.
     *
     * @return void
     */
    public function addPopup(ConvertToGeoJsonEvent $event)
    {
        $feature    = $event->getGeoJson();
        $definition = $event->getDefinition();

        if ($definition instanceof HasPopup && $feature instanceof Feature) {
            if ($definition->getPopup()) {
                $feature->setProperty('popup', $definition->getPopup());
            }

            if ($definition->getPopupContent()) {
                $feature->setProperty('popupContent', $definition->getPopupContent());
            }
        }
    }

    /**
     * @param ConvertToGeoJsonEvent $event
     */
    public function enrichMarker(ConvertToGeoJsonEvent $event)
    {
        $feature    = $event->getGeoJson();
        $definition = $event->getDefinition();
        $model      = $event->getModel();

        if ($definition instanceof Marker && $model instanceof MarkerModel && $feature instanceof Feature) {
            if ($model->featureData) {
                $feature->setProperty('data', json_decode($model->featureData, true));
            }

            if ($model->ignoreForBounds) {
                $feature->setProperty('ignoreForBounds', true);
            } else {
                $parent = LayerModel::findByPk($model->pid);

                if ($parent && !$parent->affectBounds) {
                    $feature->setProperty('ignoreForBounds', true);
                }
            }
        }
    }

    /**
     * @param ConvertToGeoJsonEvent $event
     */
    public function enrichVector(ConvertToGeoJsonEvent $event)
    {
        $feature    = $event->getGeoJson();
        $definition = $event->getDefinition();
        $model      = $event->getModel();

        if ($definition instanceof Vector && $model instanceof VectorModel && $feature instanceof Feature) {
            if ($model->featureData) {
                $feature->setProperty('data', json_decode($model->featureData, true));
            }

            if ($model->ignoreForBounds) {
                $feature->setProperty('ignoreForBounds', true);
            } else {
                $parent = LayerModel::findByPk($model->pid);

                if ($parent && !$parent->affectBounds) {
                    $feature->setProperty('ignoreForBounds', true);
                }
            }
        }
    }

    /**
     * Enrich the the circle with constructor arguments.
     *
     * @param ConvertToGeoJsonEvent $event The subscribed events.
     * @return void
     */
    public function enrichCircle(ConvertToGeoJsonEvent $event)
    {
        $feature    = $event->getGeoJson();
        $definition = $event->getDefinition();

        if ($definition instanceof Circle && !$definition instanceof CircleMarker && $feature instanceof Feature) {
            $feature->setProperty('arguments', array($definition->getLatLng(), $definition->getRadius()));
        }
    }
}
