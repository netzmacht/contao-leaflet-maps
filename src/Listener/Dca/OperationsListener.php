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

use Contao\Backend;
use Contao\BackendUser;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Leaflet\Model\StyleModel;

/**
 * Class OperationsListener
 */
final class OperationsListener
{
    /**
     * Backend user.
     *
     * @var BackendUser
     */
    private $user;

    /**
     * OperationsListener constructor.
     *
     * @param BackendUser $user Backend user.
     */
    public function __construct(BackendUser $user)
    {
        $this->user = $user;
    }

    /**
     * Generate the style operation.
     *
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $class      Button icon class.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    public function styleOperation(
        string $href,
        string $label,
        string $title,
        string $class,
        string $attributes
    ): string {
        return $this->renderIfPermissionIsGranted(StyleModel::getTable(), $href, $label, $title, $class, $attributes);
    }

    /**
     * Generate the icon operation.
     *
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $class      Button icon class.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    public function iconOperation(
        string $href,
        string $label,
        string $title,
        string $class,
        string $attributes
    ): string {
        return $this->renderIfPermissionIsGranted(IconModel::getTable(), $href, $label, $title, $class, $attributes);
    }

    /**
     * Generate the popup operation.
     *
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $class      Button icon class.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    public function popupOperation(
        string $href,
        string $label,
        string $title,
        string $class,
        string $attributes
    ): string {
        return $this->renderIfPermissionIsGranted(PopupModel::getTable(), $href, $label, $title, $class, $attributes);
    }

    /**
     * Check if user has permission to access the leaflet table.
     *
     * @param string $permission The table permission.
     *
     * @return bool
     */
    private function hasPermission(string $permission): bool
    {
        return (bool) $this->user->hasAccess($permission, 'leaflet_tables');
    }

    /**
     * Generate the style operation.
     *
     * @param string $permission Table permission to check.
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $class      Button icon class.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    private function renderIfPermissionIsGranted(
        string $permission,
        string $href,
        string $label,
        string $title,
        string $class,
        string $attributes
    ): string {
        if ($this->hasPermission($permission)) {
            return $this->render($href, $label, $title, $class, $attributes);
        }

        return '';
    }

    /**
     * Render a button.
     *
     * @param string $href       Button link.
     * @param string $label      Button label.
     * @param string $title      Button title.
     * @param string $class      Button icon class.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    private function render(string $href, string $label, string $title, string $class, string $attributes): string
    {
        return sprintf(
            ' <a href="%s" title="%s" class="%s" %s>%s</a>',
            Backend::addToUrl($href),
            $title,
            $class,
            $attributes,
            $label
        );
    }
}
