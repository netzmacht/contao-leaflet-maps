<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet;

use Netzmacht\Contao\Leaflet\Event\InitializeDefinitionMapperEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeEventDispatcherEvent;
use Netzmacht\Contao\Leaflet\Event\InitializeLeafletBuilderEvent;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class Boot initialize the leaflet map extension.
 *
 * @package Netzmacht\Contao\Leaflet
 */
class Boot
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Initialize definition mapper.
     *
     * @param DefinitionMapper $definitionMapper The definition mapper.
     *
     * @return DefinitionMapper
     */
    public function initializeDefinitionMapper(DefinitionMapper $definitionMapper)
    {
        $event = new InitializeDefinitionMapperEvent($definitionMapper);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $definitionMapper;
    }

    /**
     * Initialize the internal used event dispatcher of the leaflet encoding system.
     *
     * @param EventDispatcher $eventDispatcher The internal event dispatcher.
     *
     * @return EventDispatcher
     */
    public function initializeEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $event = new InitializeEventDispatcherEvent($eventDispatcher);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $eventDispatcher;
    }

    /**
     * Initialize the leaflet builder.
     *
     * @param Leaflet $leaflet The leaflet builder.
     *
     * @return Leaflet
     */
    public function initializeLeafletBuilder(Leaflet $leaflet)
    {
        $event = new InitializeLeafletBuilderEvent($leaflet);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $leaflet;
    }
}
