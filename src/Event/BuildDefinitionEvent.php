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

namespace Netzmacht\Contao\Leaflet\Event;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\LeafletPHP\Definition;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildDefinitionEvent is emitted when the mapper maps between the model and the definition.
 *
 * @package Netzmacht\Contao\Leaflet\Event
 */
class BuildDefinitionEvent extends Event
{
    const NAME = 'netzmacht.contao_leaflet.mapper.build_definition';

    /**
     * The leaflet object definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * The model.
     *
     * @var Model
     */
    private $model;

    /**
     * Building request.
     *
     * @var Request|null
     */
    private $request;

    /**
     * Construct.
     *
     * @param Definition   $definition The leaflet definition.
     * @param Model        $model      The definition model.
     * @param Request|null $request    Building request.
     */
    public function __construct(Definition $definition, Model $model, Request $request = null)
    {
        $this->definition = $definition;
        $this->model      = $model;
        $this->request    = $request;
    }

    /**
     * Get the definition.
     *
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Get the model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the bounds.
     *
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }
}
