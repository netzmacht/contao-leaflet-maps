<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Listener;

use Netzmacht\Contao\Leaflet\Event\GetHashEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HashSubscriber creates hashes for models and for data which does not have a hash created by other subscribers.
 *
 * @package Netzmacht\Contao\Leaflet\Subscriber
 */
class HashSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GetHashEvent::NAME => array(
                array('getModelHash'),
                array('getFallback', -100)
            )
        );
    }

    /**
     * Get hash for a model object.
     *
     * @param GetHashEvent $event The subscribed event.
     *
     * @return void
     */
    public function getModelHash(GetHashEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof \Model) {
            $event->setHash($data->getTable() . '::' . $data->{$data->getPk()});
        }
    }

    /**
     * Get hash fallback if no hash was created so far.
     *
     * @param GetHashEvent $event The subscribed event.
     *
     * @return void
     */
    public function getFallback(GetHashEvent $event)
    {
        if ($event->getHash()) {
            return;
        }

        $data = $event->getData();

        if (is_object($data)) {
            $event->setHash(spl_object_hash($data));
        } else {
            $event->setHash(md5(json_encode($data)));
        }
    }
}
