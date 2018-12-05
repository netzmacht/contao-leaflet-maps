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
use Netzmacht\Contao\Leaflet\Model\IconModel;

/**
 * Class IconDcaListener.
 *
 * @package Netzmacht\Contao\Leaflet\Listener\Dca
 */
class IconDcaListener
{
    /**
     * Contao backend user.
     *
     * @var BackendUser
     */
    private $user;

    /**
     * Icon type options.
     *
     * @var array
     */
    private $icons;

    /**
     * IconDcaListener constructor.
     *
     * @param BackendUser $user  Backend user.
     * @param array       $icons Icon type options.
     */
    public function __construct(BackendUser $user, array $icons)
    {
        $this->user  = $user;
        $this->icons = $icons;
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
        if ($this->user->hasAccess(IconModel::getTable(), 'leaflet_tables')) {
            return;
        }

        throw new AccessDeniedException(
            sprintf('Access denied to "%s" for user "%s"', IconModel::getTable(), $this->user->id)
        );
    }

    /**
     * Get icon options.
     *
     * @return array
     */
    public function getIconOptions(): array
    {
        return $this->icons;
    }
}
