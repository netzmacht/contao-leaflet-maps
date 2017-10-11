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

namespace Netzmacht\Contao\Leaflet\Mapper\Layer;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Request\Request;
use Netzmacht\LeafletPHP\Definition;

/**
 * Class ProviderLayerMapper maps the layer model to the tile provider definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Layer
 */
class ProviderLayerMapper extends AbstractLayerMapper
{
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
     * @param array $providers Registered providers.
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassName(Model $model, DefinitionMapper $mapper, Request $request = null)
    {
        if (isset($this->providers[$model->tile_provider]['class'])) {
            return $this->providers[$model->tile_provider]['class'];
        }

        return 'Netzmacht\LeafletPHP\Plugins\LeafletProviders\Provider';
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
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
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        return array(
            $model->alias ?: ('layer_' . $model->id),
            $model->tile_provider,
            $model->tile_provider_variant ?: null
        );
    }
}
