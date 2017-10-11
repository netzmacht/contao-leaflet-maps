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

namespace Netzmacht\Contao\Leaflet\Mapper;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition;

/**
 * Interface Mapper describes the Mapper which translates a given configuration to the Leaflet definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
interface Mapper
{
    /**
     * Map model to the definition.
     *
     * @param Model|mixed      $model     The model being built. Usually a contao model, but can be anything.
     * @param DefinitionMapper $mapper    The definition builder.
     * @param Request          $request   Optional building request.
     * @param string           $elementId Optional element.
     * @param Definition|null  $parent    Optional passed parent.
     *
     * @return Definition
     */
    public function handle(
        $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null,
        Definition $parent = null
    );

    /**
     * Check if mapper is responsible for the model.
     *
     * @param Model   $model   The model being build.
     * @param Request $request Optional building request.
     *
     * @return bool
     */
    public function match($model, Request $request = null);
}
