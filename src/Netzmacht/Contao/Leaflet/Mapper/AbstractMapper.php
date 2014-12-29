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

/**
 * Class AbstractBuilder.
 *
 * @package Netzmacht\Contao\Leaflet\Builder
 */
abstract class AbstractMapper implements Mapper
{
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
    public function addConditionalOption($column, $option, $mapping = null, $value = '1')
    {
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
    public function addConditionalOptions($column, array $options, $value = '1')
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
    public function handle(\Model $model, DefinitionMapper $builder)
    {
        $definition = $this->createInstance($model, $builder);

        $this->buildOptions($definition, $model);
        $this->buildConditionals($definition, $model);
        $this->doBuild($definition, $model, $builder);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Model $model)
    {
        $modelClass = static::$modelClass;

        return ($model instanceof $modelClass);
    }

    /**
     * Initialize the builder.
     *
     * @return void
     */
    abstract protected function initialize();

    /**
     * Use for specific build methods.
     *
     * @param Definition $definition The definition being built.
     * @param \Model     $model      The model.
     * @param DefinitionMapper    $builder    The definition builder.
     *
     * @return void
     */
    protected function doBuild(Definition $definition, \Model $model, DefinitionMapper $builder)
    {
    }

    /**
     * Create a new definition instance.
     *
     * @param \Model           $model  The model.
     * @param DefinitionMapper $mapper The definition mapper.
     *
     * @return Definition
     */
    protected function createInstance(\Model $model, DefinitionMapper $mapper)
    {
        $reflector = new \ReflectionClass(static::$definitionClass);
        $instance  = $reflector->newInstanceArgs($this->buildConstructArguments($model, $mapper));

        return $instance;
    }

    /**
     * Get construct arguments.
     *
     * @param \Model           $model  The model.
     * @param DefinitionMapper $mapper The definition mapper.
     *
     * @return array
     */
    protected function buildConstructArguments(\Model $model, DefinitionMapper $mapper)
    {
        return array(
            $model->alias ?: $model->id
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
                if ($model->$column == $value) {
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
     */
    private function applyOptions($options, $definition, $model)
    {
        foreach ($options as $option => $mapping) {
            $setter = 'set' . ucfirst($option);

            if ($model->$option != $this->getDefaultOption($option, $definition)) {
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
}
