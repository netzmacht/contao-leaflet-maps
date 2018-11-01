<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Frontend\Action;

use Netzmacht\Contao\Leaflet\Filter\Filter;
use Netzmacht\Contao\Leaflet\Filter\FilterFactory;
use Netzmacht\Contao\Leaflet\MapProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Request action which handles request for layer data
 */
final class LayerDataAction
{
    /**
     * Map provider.
     *
     * @var MapProvider
     */
    private $mapProvider;

    /**
     * Filter factory.
     *
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * LayerDataAction constructor.
     *
     * @param MapProvider   $mapProvider   Map provider.
     * @param FilterFactory $filterFactory Filter factory.
     */
    public function __construct(MapProvider $mapProvider, FilterFactory $filterFactory)
    {
        $this->mapProvider   = $mapProvider;
        $this->filterFactory = $filterFactory;
    }

    /**
     * Handle the request.
     *
     * @param int     $layerId The layer id.
     * @param string  $_format The requested output format.
     * @param Request $request The request.
     *
     * @return Response
     *
     * @throws BadRequestHttpException When unsupported format is given.
     *
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function __invoke(int $layerId, string $_format, Request $request): Response
    {
        $filter = $this->createFilter($request);
        $data   = $this->mapProvider->getFeatureCollection($layerId, $filter);

        if ($_format === 'geojson') {
            $response = new JsonResponse($data);
            $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);

            return $response;
        }

        throw new BadRequestHttpException(sprintf('Unsupported format "%s"', $_format));
    }

    /**
     * Create the filter if defined in the request.
     *
     * @param Request $request The request.
     *
     * @return Filter|null
     */
    private function createFilter(Request $request): ?Filter
    {
        if (!$request->query->has('filter')) {
            return null;
        }

        $filter = (string) $request->query->get('filter');
        $values = (string) $request->query->get('values');

        return $this->filterFactory->create($filter, $values);
    }
}
