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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Netzmacht\Contao\Leaflet\Model\StyleModel;

/**
 * Class StyleDcaListener.
 *
 * @package Netzmacht\Contao\Leaflet\Listener\Dca
 */
class StyleDcaListener
{
    /**
     * Backend user.
     *
     * @var BackendUser
     */
    private $user;

    /**
     * Style type options.
     *
     * @var array
     */
    private $icons;

    /**
     * StyleDcaListener constructor.
     *
     * @param BackendUser $user   Backend user.
     * @param array       $styles Styles options.
     */
    public function __construct(BackendUser $user, array $styles)
    {
        $this->icons = $styles;
        $this->user  = $user;
    }

    /**
     * Check the permission.
     *
     * @return void
     *
     * @throws AccessDeniedException If user has not the permission.
     */
    public function checkPermission(): void
    {
        if ($this->user->hasAccess(StyleModel::getTable(), 'leaflet_tables')) {
            return;
        }

        throw new AccessDeniedException(
            sprintf('Access denied to "%s" for user "%s"', StyleModel::getTable(), $this->user->id)
        );
    }

    /**
     * Get style options.
     *
     * @return array
     */
    public function getStyleOptions(): array
    {
        return $this->icons;
    }
}
