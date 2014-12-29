<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\LeafletPHP\Definition;

interface Mapper
{
    /**
     * Map model to the definition.
     *
     * @param \Model  $model   The model being built.
     * @param DefinitionMapper $builder The definition builder.
     *
     * @return Definition
     */
    public function handle(\Model $model, DefinitionMapper $builder);

    /**
     * Check if builder is responsible for the model.
     *
     * @param \Model $model The model being build.
     *
     * @return bool
     */
    public function match(\Model $model);
}
