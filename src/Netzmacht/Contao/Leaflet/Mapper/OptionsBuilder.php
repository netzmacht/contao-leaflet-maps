<?php

/**
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper;

use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\HasOptions;

/**
 * Class OptionsBuilder handles the option mapping between the database model and the definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper
 */
class OptionsBuilder
{
    const VALUE_NOT_EMPTY = '__value_not_empty__';
    const VALUE_EMPTY     = '__value_empty__';

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
     * Build options and conditional options.
     *
     * @param Definition $definition The definition being built.
     * @param \Model     $model      The model.
     *
     * @return $this
     */
    public function build($definition, $model)
    {
        $this->buildOptions($definition, $model);
        $this->buildConditionals($definition, $model);

        return $this;
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
    public static function applyOptions($options, $definition, $model)
    {
        foreach ($options as $option => $mapping) {
            $default = static::getDefaultOption($option, $definition);

            if ($model->$mapping === '1' || $model->$mapping === '') {
                if (((bool) $model->$mapping) !== $default) {
                    static::applyOption($option, $model->$mapping, $definition);
                }
            } elseif (is_numeric($default)) {
                if ($model->$mapping != $default) {
                    static::applyOption($option, $model->$mapping, $definition);
                }
            } elseif ($model->$mapping !== $default) {
                static::applyOption($option, $model->$mapping, $definition);
            }
        }
    }

    /**
     * Apply an option.
     *
     * @param string     $option     The option name.
     * @param mixed      $value      The option value.
     * @param Definition $definition The definition.
     *
     * @return void
     */
    private static function applyOption($option, $value, $definition)
    {
        $setter = 'set' . ucfirst($option);

        if (method_exists($definition, $setter)) {
            $definition->$setter($value);
        } elseif ($definition instanceof HasOptions) {
            $definition->setOption($option, $value);
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
    private static function getDefaultOption($option, $definition)
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
