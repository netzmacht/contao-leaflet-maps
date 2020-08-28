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

namespace Netzmacht\Contao\Leaflet\Bundle\ConsentBridge;

use Hofff\Contao\Consent\Bridge\Bridge;
use Hofff\Contao\Consent\Bridge\Plugin as ConsentBridgePlugin;
use Hofff\Contao\Consent\Bridge\Render\RenderInformation;

/**
 * Consent Bridge plugin for leaflet.
 */
final class Plugin implements ConsentBridgePlugin
{
    /**
     * {@inheritDoc}
     */
    public function load(Bridge $bridge): void
    {
        $renderInformation = RenderInformation::autoRenderWithPlaceholder('leaflet_consent_bridge_placeholder');

        $bridge
            ->supportContentElement('leaflet', $renderInformation)
            ->supportFrontendModule('leaflet', $renderInformation);
    }
}
