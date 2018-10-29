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

namespace Netzmacht\Contao\Leaflet\Frontend;

use Netzmacht\Contao\PageContext\Exception\DeterminePageIdFailed;
use Netzmacht\Contao\PageContext\Request\PageIdDeterminator as PageContextPageIdDeterminator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiPageIdDeterminator
 */
final class PageIdDeterminator implements PageContextPageIdDeterminator
{
    /**
     * {@inheritDoc}
     */
    public function match(Request $request): bool
    {
        return ($request->attributes->get('_leaflet_scope') === 'page' && $request->query->get('context') === 'page');
    }

    /**
     * {@inheritDoc}
     *
     * @throws DeterminePageIdFailed When no context id is given.
     */
    public function determinate(Request $request): int
    {
        if (!$request->query->has('contextId')) {
            throw new DeterminePageIdFailed('Could not determine page id for from request.');
        }

        return $request->query->getInt('contextId');
    }
}
