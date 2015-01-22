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

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\OptionsBuilder;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

/**
 * Class ProviderLayerMapper maps the layer model to the tile provider definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class ProviderLayerMapper extends AbstractLayerMapper
{
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
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        if (isset($this->providers[$model->tile_provider]['class'])) {
            return $this->providers[$model->tile_provider]['class'];
        }

        return parent::getClassName($model, $mapper, $bounds);
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        if (!empty($this->providers[$model->tile_provider]['options'])) {
            OptionsBuilder::applyOptions(
                $this->providers[$model->tile_provider]['options'],
                $definition,
                $model
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        $elementId = null
    ) {
        return array(
            $model->alias ?: ('layer_' . $model->id),
            $model->tile_provider,
            $model->tile_provider_variant ?: null
        );
    }
}
