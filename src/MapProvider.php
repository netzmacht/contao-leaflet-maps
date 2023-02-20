<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet;

use Contao\Input;
use Netzmacht\Contao\Leaflet\Encoder\ContaoAssets;
use Netzmacht\Contao\Leaflet\Event\GetJavascriptEvent;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Frontend\DataController;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Leaflet;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Contracts\Cache\CacheInterface as Cache;

/**
 * Class MapProvider.
 *
 * @package Netzmacht\Contao\Leaflet
 */
class MapProvider
{
    /**
     * The definition mapper.
     *
     * @var DefinitionMapper
     */
    private $mapper;

    /**
     * The leaflet service.
     *
     * @var Leaflet
     */
    private $leaflet;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * The request input.
     *
     * @var \Input
     */
    private $input;

    /**
     * Map assets collector.
     *
     * @var ContaoAssets
     */
    private $assets;

    /**
     * Cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Data controller.
     *
     * @var DataController
     */
    private $dataController;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Template renderer.
     *
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * Construct.
     *
     * @param DefinitionMapper  $mapper            The definition mapper.
     * @param Leaflet           $leaflet           The Leaflet instance.
     * @param EventDispatcher   $eventDispatcher   The Contao event dispatcher.
     * @param Input             $input             Thw request input.
     * @param ContaoAssets      $assets            Assets handler.
     * @param Cache             $cache             Cache.
     * @param DataController    $dataController    Data controller.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param TemplateRenderer  $templateRenderer  Template rednerer.
     */
    public function __construct(
        DefinitionMapper $mapper,
        Leaflet $leaflet,
        EventDispatcher $eventDispatcher,
        $input,
        ContaoAssets $assets,
        Cache $cache,
        DataController $dataController,
        RepositoryManager $repositoryManager,
        TemplateRenderer $templateRenderer
    ) {
        $this->mapper            = $mapper;
        $this->leaflet           = $leaflet;
        $this->eventDispatcher   = $eventDispatcher;
        $this->input             = $input;
        $this->assets            = $assets;
        $this->cache             = $cache;
        $this->dataController    = $dataController;
        $this->repositoryManager = $repositoryManager;
        $this->templateRenderer  = $templateRenderer;
    }

    /**
     * Get map definition.
     *
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param Filter       $filter    Optional request filter.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     *
     * @return Map
     */
    public function getDefinition($mapId, Filter $filter = null, $elementId = null)
    {
        if ($mapId instanceof MapModel) {
            $model = $mapId;
            $mapId = $model->id;
        } else {
            $model = $this->getModel($mapId);
        }

        $request    = new Request($elementId ?: $mapId, $filter);
        $definition = $this->mapper->reset()->handle($model, $request, $elementId);

        return $definition;
    }

    /**
     * Get map model.
     *
     * @param int|string $mapId Model id or alias.
     *
     * @return MapModel
     *
     * @throws \InvalidArgumentException If no model is found.
     */
    public function getModel($mapId)
    {
        $repository = $this->repositoryManager->getRepository(MapModel::class);
        $model      = $repository->findByIdOrAlias($mapId);

        if ($model === null) {
            throw new \InvalidArgumentException(sprintf('Model "%s" not found', $mapId));
        }

        return $model;
    }

    /**
     * Get map javascript.
     *
     * @param MapModel|int $mapId     The map database id. MapModel accepted as well.
     * @param Filter       $filter    Optional request filter.
     * @param string       $elementId Optional element id. If none given the mapId or alias is used.
     * @param string       $template  The template being used for generating.
     * @param string       $style     Optional style attributes.
     *
     * @return string
     * @throws \Exception If generating went wrong.
     */
    public function generate(
        $mapId,
        Filter $filter = null,
        $elementId = null,
        $template = 'leaflet_map_js',
        $style = ''
    ) {
        if ($mapId instanceof MapModel) {
            $model = $mapId;
            $mapId = $mapId->id;
        } else {
            $model = $this->getModel($mapId);
        }

        $cacheKey = null;
        $doCache  = $model->cache;

        if ($doCache) {
            $cacheKey = $this->getCacheKey($mapId, $filter, $elementId, $template, $style);

            if ($this->cache->hasItem($cacheKey)) {
                $cached     = $this->cache->getItem($cacheKey);
                $cachedData = $cached->get();
                $this->assets->fromArray($cachedData['assets']);

                return $cachedData['javascript'];
            } else {
                $cached = $this->cache->getItem($cacheKey);
            }
        }

        $buffer = $this->doGenerate($model, $filter, $elementId, $template, $style);

        if ($doCache) {
            $cached
                ->expiresAfter((int) $model->cacheLifeTime)
                ->set(
                    [
                        'assets'     => $this->assets->toArray(),
                        'javascript' => $buffer,
                    ]
                );
            $this->cache->save($cached);
        }

        return $buffer;
    }

    /**
     * Get feature collection of a layer.
     *
     * @param LayerModel|int $layerId The layer database id or layer model.
     * @param Filter|null    $filter  Filter data.
     *
     * @return FeatureCollection
     *
     * @throws \InvalidArgumentException If a layer could not be found.
     */
    public function getFeatureCollection($layerId, Filter $filter = null)
    {
        if ($layerId instanceof LayerModel) {
            $model = $layerId;
        } else {
            $repository = $this->repositoryManager->getRepository(LayerModel::class);
            $model      = $repository->find((int) $layerId);
        }

        if (!$model || !$model->active) {
            throw new \InvalidArgumentException(sprintf('Could not find layer "%s"', $layerId));
        }

        $request = new Request('', $filter);

        if (!$model->cache) {
            return $this->mapper->handleGeoJson($model, $request);
        }

        $cacheKey = 'feature_layer_' . $model->id;
        if ($filter) {
            $cacheKey .= '.filter_' . md5($filter->toRequest());
        }

        if ($this->cache->hasItem($cacheKey)) {
            $cachedItem = $this->cache->getItem($cacheKey);

            return $cachedItem->get();
        } else {
            $cachedItem = $this->cache->getItem($cacheKey);
        }

        $collection = $this->mapper->handleGeoJson($model, $request);
        $cachedItem
            ->expiresAfter($model->cacheLifeTime)
            ->set($collection);
        $this->cache->save($cachedItem);

        return $collection;
    }

    /**
     * Handle ajax request.
     *
     * @param string $identifier The request identifier.
     * @param bool   $exit       Exit if ajax request is detected.
     *
     * @return void
     * @throws \RuntimeException IF the input data does not match.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function handleAjaxRequest($identifier, $exit = true)
    {
        $input = $this->input->get('leaflet', true);

        // Handle ajax request.
        if ($input) {
            $data   = explode(',', base64_decode($input));
            $data[] = $this->input->get('f');
            $data[] = $this->input->get('v');

            if (count($data) != 6) {
                throw new \RuntimeException('Bad request. Could not resolve query params');
            }

            $data = array_combine(['for', 'type', 'id', 'format', 'filter', 'values'], $data);
            $data = array_filter($data);

            if (empty($data['for']) || $data['for'] != $identifier) {
                return;
            }

            $this->dataController->execute($data, $this);

            if ($exit) {
                exit;
            }
        }
    }

    /**
     * Get the cache key.
     *
     * @param int         $mapId     The map database id.
     * @param Filter|null $filter    Optional request filter.
     * @param string      $elementId Optional element id. If none given the mapId or alias is used.
     * @param string      $template  The template being used for generating.
     * @param string      $style     Optional style attributes.
     *
     * @return string
     */
    protected function getCacheKey($mapId, $filter, $elementId, $template, $style)
    {
        $cacheKey = 'map_' . $mapId;

        if ($filter) {
            $cacheKey .= '.filter_' . md5($filter->toRequest());
        }

        if ($elementId) {
            $cacheKey .= '.element_' . $elementId;
        }

        $cacheKey .= '.template_' . $template;

        if ($style) {
            $cacheKey .= '.style_' . md5($style);

            return $cacheKey;
        }

        return $cacheKey;
    }

    /**
     * Do the generating of the map.
     *
     * @param MapModel    $model     Map model.
     * @param Filter|null $filter    Optional request filter.
     * @param string      $elementId Optional element id. If none given the mapId or alias is used.
     * @param string      $template  The template being used for generating.
     * @param string      $style     Optional style attributes.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function doGenerate($model, $filter, $elementId, $template, $style)
    {
        $definition = $this->getDefinition($model, $filter, $elementId);
        $javascript = $this->leaflet->build($definition, $this->assets);
        $mapId      = $definition->getId();

        $templateReference = 'fe:' . $template;
        $parameters        = [
            'definition' => $definition,
            'model'      => $model,
            'elementId'  => $elementId,
            'style'      => $style,
            'javascript' => $javascript,
            'mapId'      => $mapId,
        ];

        $content = $this->templateRenderer->render($templateReference, $parameters);
        $content = preg_replace(
            ['/^<!-- TEMPLATE (START): .+ -->\n*/', '/\n*<!-- TEMPLATE (END): .+ -->$/'],
            '',
            $content
        );

        $event = new GetJavascriptEvent($definition, $content);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        return $event->getJavascript();
    }
}
