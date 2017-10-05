<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Subscriber;

use Netzmacht\Contao\Leaflet\Dca\VectorCallbacks;
use Netzmacht\Contao\Leaflet\Event\ConvertToGeoJsonEvent;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\LeafletPHP\Value\GeoJson\Feature;
use Netzmacht\LeafletPHP\Definition\HasPopup;
use Netzmacht\LeafletPHP\Definition\UI\Marker;
use Netzmacht\LeafletPHP\Definition\Vector\Circle;
use Netzmacht\LeafletPHP\Definition\Vector\CircleMarker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class GeoJsonSubscriber provides subscribers when a definition is converted to a geo json feature.
 *
 * @package Netzmacht\Contao\Leaflet\Subscriber
 */
class GeoJsonSubscriber implements EventSubscriberInterface
{
    /**
     * Property mapping between models and features.
     *
     * @var array
     */
    private $featureModelProperties;

    /**
     * GeoJsonSubscriber constructor.
     *
     * @param array $featureModelProperties Property mapping between models and features.
     */
    public function __construct(array $featureModelProperties)
    {
        $this->featureModelProperties = $featureModelProperties;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConvertToGeoJsonEvent::NAME => array(
                array('addPopup'),
                array('enrichObjects'),
                array('enrichCircle'),
                array('setModelData')
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

            if ($definition->getPopupOptions()) {
                $feature->setProperty('popupOptions', $definition->getPopupOptions());
            }
        }
    }

    /**
     * Enrich map object with feature data and bounds information.
     *
     * @param ConvertToGeoJsonEvent $event The subscribed event.
     *
     * @return void
     */
    public function enrichObjects(ConvertToGeoJsonEvent $event)
    {
        $feature    = $event->getGeoJson();
        $definition = $event->getDefinition();
        $model      = $event->getModel();

        if (($definition instanceof Marker || $definition instanceof VectorCallbacks)
            && $model instanceof \Model && $feature instanceof Feature) {
            $this->setDataProperty($model, $feature);
            $this->setBoundsInformation($model, $feature);
        }
    }

    /**
     * Enrich the the circle with constructor arguments.
     *
     * @param ConvertToGeoJsonEvent $event The subscribed events.
     *
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

    /**
     * Pass configured properties on an model to  the properties.model key.
     *
     * @param ConvertToGeoJsonEvent $event The subscribed events.
     *
     * @return void
     */
    public function setModelData(ConvertToGeoJsonEvent $event)
    {
        $feature = $event->getGeoJson();
        $model   = $event->getModel();

        if (!$model instanceof \Model || !$feature instanceof Feature
            || empty($this->featureModelProperties[$model->getTable()])) {
            return;
        }

        $mapping = $this->featureModelProperties[$model->getTable()];
        $data    = (array) $feature->getProperty('model');

        foreach ((array) $mapping as $property) {
            $value = $this->parseModelValue($model, $property);

            // Important: Do not combine with line above as the property can be modified if it's an array.
            $data[$property] = $value;
        }

        $feature->setProperty('model', $data);
    }

    /**
     * Parse the model value based on the config.
     *
     * @param \Model $model    The model.
     * @param mixed  $property The property config.
     *
     * @return array|mixed|null
     */
    private function parseModelValue(\Model $model, &$property)
    {
        if (is_array($property)) {
            list($property, $type) = $property;
            $value                 = $model->$property;

            switch ($type) {
                case 'array':
                case 'object':
                    $value = deserialize($value, true);
                    break;

                case 'file':
                    $file  = \FilesModel::findByUuid($value);
                    $value = $file->path;
                    break;

                case 'files':
                    $collection = \FilesModel::findMultipleByUuids(deserialize($value, true));

                    if ($collection) {
                        $value = $collection->fetchEach('path');
                    } else {
                        $value = array();
                    }
                    break;

                default:
                    $value = null;
            }
        } else {
            $value = $model->$property;
        }

        return $value;
    }

    /**
     * Set the bounds information.
     *
     * @param \Model  $model   The model.
     * @param Feature $feature The feature.
     *
     * @return void
     */
    protected function setBoundsInformation($model, $feature)
    {
        if ($model->ignoreForBounds) {
            $feature->setProperty('ignoreForBounds', true);
        } else {
            $parent = LayerModel::findByPk($model->pid);

            if ($parent && $parent->boundsMode !== 'extend') {
                $feature->setProperty('ignoreForBounds', true);
            }
        }
    }

    /**
     * Set the data property.
     *
     * @param \Model  $model   The model.
     * @param Feature $feature The feature.
     *
     * @return void
     */
    protected function setDataProperty($model, $feature)
    {
        if ($model->featureData) {
            $feature->setProperty('data', json_decode($model->featureData, true));
        }
    }
}
