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

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\LeafletPHP\Definition;

/**
 * Class AbstractMapper is made for mapping Contao models to the definition.
 *
 * For custom sources besides Contao models use your own implementation of the mapper interface.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
abstract class AbstractMapper implements Mapper
{
    const VALUE_NOT_EMPTY = '__value_not_empty__';
    const VALUE_EMPTY     = '__value_empty__';

    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = null;

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = null;

    /**
     * Options builder.
     *
     * @var OptionsBuilder
     */
    protected $optionsBuilder;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->optionsBuilder = new OptionsBuilder();
        $this->initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        $elementId = null,
        Definition $parent = null
    ) {
        $definition = $this->createInstance($model, $mapper, $filter, $elementId);

        $this->optionsBuilder->build($definition, $model);
        $this->build($definition, $model, $mapper, $filter, $parent);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function match($model, Filter $filter = null)
    {
        $modelClass = static::$modelClass;

        return ($model instanceof $modelClass);
    }

    /**
     * Initialize the mapper.
     *
     * @return void
     */
    protected function initialize()
    {
    }

    /**
     * Use for specific build methods.
     *
     * @param Definition       $definition The definition being built.
     * @param \Model           $model      The model.
     * @param DefinitionMapper $mapper     The definition mapper.
     * @param Filter|null      $filter     Optional request filter.
     * @param Definition|null  $parent     The parent object.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        Definition $parent = null
    ) {
    }

    /**
     * Create a new definition instance.
     *
     * @param \Model           $model     The model.
     * @param DefinitionMapper $mapper    The definition mapper.
     * @param Filter           $filter    Optional request filter.
     * @param string|null      $elementId Optional element id.
     *
     * @return Definition
     */
    protected function createInstance(
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        $elementId = null
    ) {
        $reflector = new \ReflectionClass($this->getClassName($model, $mapper, $filter));
        $instance  = $reflector->newInstanceArgs($this->buildConstructArguments($model, $mapper, $filter, $elementId));

        return $instance;
    }

    /**
     * Get construct arguments.
     *
     * @param \Model           $model     The model.
     * @param DefinitionMapper $mapper    The definition mapper.
     * @param Filter           $filter    Optional request filter.
     * @param string|null      $elementId Optional element id.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        Filter $filter = null,
        $elementId = null
    ) {
        return array(
            $this->getElementId($model, $elementId)
        );
    }

    /**
     * Get definition class name.
     *
     * @param \Model           $model  The model.
     * @param DefinitionMapper $mapper The definition mapper.
     * @param Filter           $filter Optional request filter.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, Filter $filter = null)
    {
        return static::$definitionClass;
    }

    /**
     * Create element id for the model.
     *
     * @param \Model      $model     The model being passed.
     * @param string|null $elementId Optional forced id.
     *
     * @return string
     */
    protected function getElementId(\Model $model, $elementId = null)
    {
        if ($elementId) {
            return $elementId;
        }

        return $model->alias ?: (str_replace('tl_leaflet_', '', $model->getTable()) . '_' . $model->id);
    }
}
