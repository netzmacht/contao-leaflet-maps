<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;


use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;

class ProviderLayerMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\LayerModel';

    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Plugins\LeafletProviders\Provider';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'provider';

    /**
     * Registered providers.
     *
     * @var array
     */
    private $providers;

    /**
     * Construct.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        $this->providers = &$GLOBALS['LEAFLET_TILE_PROVIDERS'];

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function createInstance(\Model $model, DefinitionMapper $mapper)
    {
        if (isset($this->providers[$model->tile_provider]['class'])) {
            $class = $this->providers[$model->tile_provider]['class'];
        } else {
            $class = static::$definitionClass;
        }

        $reflector = new \ReflectionClass($class);
        $instance  = $reflector->newInstanceArgs($this->buildConstructArguments($model, $mapper));

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    protected function doBuild(Definition $definition, \Model $model, DefinitionMapper $builder)
    {
        if (!empty($this->providers[$model->tile_provider]['options'])) {
            $this->applyOptions(
                $this->providers[$model->tile_provider]['options'],
                $definition,
                $model
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(\Model $model, DefinitionMapper $mapper)
    {
        return array(
            $model->alias ?: ('layer_' . $model->id),
            $model->tile_provider,
            $model->tile_provider_variant ?: null
        );
    }
}
