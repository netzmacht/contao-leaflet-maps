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

namespace Netzmacht\Contao\Leaflet\Event;

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InitializeDefinitionMapperEvent is emitted when the definition mapper is created.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class InitializeDefinitionMapperEvent extends Event
{
    const NAME = 'leaflet.boot.initialize-definition-mapper';

    /**
     * The definitino mapper.
     *
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
