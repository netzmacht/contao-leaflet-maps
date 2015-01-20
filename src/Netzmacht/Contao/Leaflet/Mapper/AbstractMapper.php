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
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

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
     * Options mapping.
     *
     * @var array
     */
    private $options = array();

    /**
     * Conditional option mapping.
     *
     * @var array
     */
    private $conditional = array();

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Add a option mapping.
     *
     * @param string $option  Name of the option.
     * @param string $mapping Mapping column name. Set if column name differs.
     *
     * @return $this
     */
    public function addOption($option, $mapping = null)
    {
        if (!isset($this->options[$option])) {
            $this->options[$option] = $this->getMapping($option, $mapping);
        }

        return $this;
    }

    /**
     * Add options mapping.
     *
     * @param array|mixed $options List of option names.
     *
     * @return $this
     */
    public function addOptions($options)
    {
        $arguments = func_get_args();

        if (count($arguments) > 1) {
            $options = $arguments;
        }

        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $this->addOption($value);
            } else {
                $this->addOption($key, $value);
            }
        }

        return $this;
    }

    /**
     * Add a conditional option.
     *
     * @param string $column  Condition column.
     * @param string $option  Option name.
     * @param null   $mapping Mapping column name.
     * @param mixed  $value   Value of the conditional column.
     *
     * @return $this
     */
    public function addConditionalOption($column, $option = null, $mapping = null, $value = self::VALUE_NOT_EMPTY)
    {
        $option = $option ?: $column;

        if (!isset($this->conditional[$column][$value][$option])) {
            $this->conditional[$column][$value][$option] = $this->getMapping($option, $mapping);
        }

        return $this;
    }

    /**
     * Add a conditional options.
     *
     * @param string $column  Condition column.
     * @param array  $options Option names.
     * @param mixed  $value   Value of the conditional column.
     *
     * @return $this
     */
    public function addConditionalOptions($column, array $options, $value = self::VALUE_NOT_EMPTY)
    {
        foreach ($options as $key => $option) {
            if (is_numeric($key)) {
                $this->addConditionalOption($column, $option, null, $value);
            } else {
                $this->addConditionalOption($column, $key, $option, $value);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null,
        Definition $parent = null
    ) {
        $definition = $this->createInstance($model, $mapper, $bounds, $elementId);

        $this->buildOptions($definition, $model);
        $this->buildConditionals($definition, $model);
        $this->build($definition, $model, $mapper, $bounds, $parent);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function match($model, LatLngBounds $bounds = null)
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
     * @param LatLngBounds     $bounds     Optional bounds where elements should be in.
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
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
    }

    /**
     * Create a new definition instance.
     *
     * @param \Model           $model     The model.
     * @param DefinitionMapper $mapper    The definition mapper.
     * @param LatLngBounds     $bounds    Optional bounds where elements should be in.
     * @param string|null      $elementId Optional element id.
     *
     * @return Definition
     */
    protected function createInstance(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        $reflector = new \ReflectionClass($this->getClassName($model, $mapper, $bounds));
        $instance  = $reflector->newInstanceArgs($this->buildConstructArguments($model, $mapper, $bounds, $elementId));

        return $instance;
    }

    /**
     * Get construct arguments.
     *
     * @param \Model           $model     The model.
     * @param DefinitionMapper $mapper    The definition mapper.
     * @param LatLngBounds     $bounds    Optional bounds where elements should be in.
     * @param string|null      $elementId Optional element id.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        return array(
            $this->getElementId($model, $elementId)
        );
    }

    /**
     * Build options.
     *
     * @param Definition $definition The definition being built.
     * @param \Model     $model      The model.
     *
     * @return void
     */
    private function buildOptions($definition, $model)
    {
        $this->applyOptions($this->options, $definition, $model);
    }

    /**
     * Build conditional options.
     *
     * @param Definition $definition The definition being built.
     * @param \Model     $model      The model.
     *
     * @return void
     */
    private function buildConditionals(Definition $definition, \Model $model)
    {
        foreach ($this->conditional as $column => $conditions) {
            foreach ($conditions as $value => $options) {
                if ($value === static::VALUE_EMPTY && empty($model->$column)) {
                    $this->applyOptions($options, $definition, $model);
                } elseif ($value === static::VALUE_NOT_EMPTY && !empty($model->$column)) {
                    $this->applyOptions($options, $definition, $model);
                } elseif ($model->$column == $value) {
                    $this->applyOptions($options, $definition, $model);
                }
            }
        }
    }

    /**
     * Get the mapping column.
     *
     * @param string      $option  Option name.
     * @param string|null $mapping Mapping column.
     *
     * @return string
     */
    private function getMapping($option, $mapping)
    {
        if ($mapping === null) {
            return $option;
        }

        return $mapping;
    }

    /**
     * Apply options from the model to the definition.
     *
     * @param array      $options    The options.
     * @param Definition $definition The definition being built.
     * @param \Model     $model      The model.
     *
     * @return void
     */
    protected function applyOptions($options, $definition, $model)
    {
        foreach ($options as $option => $mapping) {
            $setter  = 'set' . ucfirst($option);
            $default = $this->getDefaultOption($option, $definition);

            if ($model->$mapping === '1' || $model->$mapping === '') {
                if (((bool) $model->$option) !== $default) {
                    $definition->$setter($model->$mapping);
                }
            } elseif ($model->$mapping !== $default) {
                $definition->$setter($model->$mapping);
            }
        }
    }

    /**
     * Get default option value.
     *
     * @param string     $option     The option name.
     * @param Definition $definition The definition being built.
     *
     * @return mixed
     */
    private function getDefaultOption($option, $definition)
    {
        $keys   = array('has', 'is', 'get');
        $suffix = ucfirst($option);

        foreach ($keys as $key) {
            $method = $key . $suffix;

            if (method_exists($definition, $method)) {
                return $definition->$method();
            }
        }

        return null;
    }

    /**
     * Get definition class name.
     *
     * @param \Model           $model  The model.
     * @param DefinitionMapper $mapper The definition mapper.
     * @param LatLngBounds     $bounds Optional bounds where elements should be in.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
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
