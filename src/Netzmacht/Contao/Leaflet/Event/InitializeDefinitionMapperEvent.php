<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Event;

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InitializeDefinitionMapperEvent
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class InitializeDefinitionMapperEvent extends Event
{
    const NAME = 'leaflet.boot.initialize-definition-mapper';

    /**
     * @var DefinitionMapper
     */
    private $definitionMapper;

    /**
     * Construct.
     *
     * @param DefinitionMapper $definitionMapper The definition mapper.
     */
    public function __construct(DefinitionMapper $definitionMapper)
    {
        $this->definitionMapper = $definitionMapper;
    }

    /**
     * Get the definition mapper.
     *
     * @return DefinitionMapper
     */
    public function getDefinitionMapper()
    {
        return $this->definitionMapper;
    }
}
