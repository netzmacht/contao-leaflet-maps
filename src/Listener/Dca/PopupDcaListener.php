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

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Netzmacht\Contao\Leaflet\Model\PopupModel;

/**
 * Class PopupDcaListener
 */
final class PopupDcaListener
{
    /**
     * Backend user.
     *
     * @var BackendUser
     */
    private $user;

    /**
     * PopupDcaListener constructor.
     *
     * @param BackendUser $user Backend user.
     */
    public function __construct(BackendUser $user)
    {
        $this->user = $user;
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
        if ($this->user->hasAccess(PopupModel::getTable(), 'leaflet_tables')) {
            return;
        }

        throw new AccessDeniedException(
            sprintf('Access denied to "%s" for user "%s"', PopupModel::getTable(), $this->user->id)
        );
    }
}
