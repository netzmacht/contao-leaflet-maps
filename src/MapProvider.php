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

namespace Netzmacht\Contao\Leaflet;

use Contao\Input;
use Doctrine\Common\Cache\Cache;
use Netzmacht\Contao\Leaflet\Event\GetJavascriptEvent;
use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Frontend\DataController;
use Netzmacht\Contao\Leaflet\Frontend\RequestUrl;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\LeafletPHP\Value\GeoJson\FeatureCollection;
use Netzmacht\LeafletPHP\Definition\Map;
use Netzmacht\LeafletPHP\Leaflet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

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
     * Request filters configuration.
     *
     * @var array
     */
    private $filters;

    /**
     * Display errors setting.
     *
     * @var bool
     */
    private $displayErrors;

    /**
     * Cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Construct.
     *
     * @param DefinitionMapper $mapper          The definition mapper.
     * @param Leaflet          $leaflet         The Leaflet instance.
     * @param EventDispatcher  $eventDispatcher The Contao event dispatcher.
     * @param Input           $input           Thw request input.
     * @param ContaoAssets     $assets          Assets handler.
     * @param Cache            $cache           Cache.
     * @param array            $filters         Request filters configuration.
     * @param bool             $displayErrors   Display errors setting.
     */
    public function __construct(
        DefinitionMapper $mapper,
        Leaflet $leaflet,
        EventDispatcher $eventDispatcher,
        $input,
        ContaoAssets $assets,
        Cache $cache,
        array $filters,
        $displayErrors
    ) {
        $this->mapper          = $mapper;
        $this->leaflet         = $leaflet;
        $this->eventDispatcher = $eventDispatcher;
        $this->input           = $input;
        $this->assets          = $assets;
        $this->filters         = $filters;
        $this->displayErrors   = $displayErrors;
        $this->cache           = $cache;
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

        RequestUrl::setFor($elementId ?: $mapId);
        $definition = $this->mapper->reset()->handle($model, $filter, $elementId);
        RequestUrl::setFor(null);

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
        $model = MapModel::findByIdOrAlias($mapId);

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

        if ($model->cache) {
            $cacheKey = $this->getCacheKey($mapId, $filter, $elementId, $template, $style);

            if ($this->cache->contains($cacheKey)) {
                $cached = $this->cache->fetch($cacheKey);
                $this->assets->fromArray($cached['assets']);

                return $cached['javascript'];
            }
        }

        $buffer = $this->doGenerate($mapId, $filter, $elementId, $template, $model, $style);

        if ($model->cache) {
            $this->cache->save(
                $cacheKey,
                [
                    'assets' => $this->assets->toArray(),
                    'javascript' => $buffer
                ],
                (int) $model->cacheLifeTime
            );
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
            $model = LayerModel::findByPK($layerId);
        }

        if (!$model || !$model->active) {
            throw new \InvalidArgumentException(sprintf('Could not find layer "%s"', $layerId));
        }

        if (!$model->cache) {
            return $this->mapper->handleGeoJson($model, $filter);
        }

        $cacheKey = 'feature_layer_' . $model->id;
        if ($filter) {
            $cacheKey .= '.filter_' . md5($filter->toRequest());
        }

        if ($this->cache->contains($cacheKey)) {
            return $this->cache->fetch($cacheKey);
        }

        $collection = $this->mapper->handleGeoJson($model, $filter);
        $this->cache->save($cacheKey, $collection, $model->cacheLifeTime);

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

            $data = array_combine(array('for', 'type', 'id', 'format', 'filter', 'values'), $data);
            $data = array_filter($data);

            if (empty($data['for']) || $data['for'] != $identifier) {
                return;
            }

            $controller = new DataController($this, $this->filters, $this->displayErrors);
            $controller->execute($data);

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
        $template   = \Controller::getTemplate($template);

        // @codingStandardsIgnoreStart - Set for the template.
        $javascript = $this->leaflet->build($definition, $this->assets);
        $mapId      = $definition->getId();
        // @codingStandardsIgnoreEnd

        ob_start();
        include $template;
        $content = ob_get_contents();
        ob_end_clean();

        $event = new GetJavascriptEvent($definition, $content);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        $buffer = $event->getJavascript();

        return $buffer;
    }
}
