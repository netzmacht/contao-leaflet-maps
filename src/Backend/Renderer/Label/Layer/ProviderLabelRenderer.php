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

namespace Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer;

use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Backend label renderer for provider layer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
final class ProviderLabelRenderer extends AbstractLabelRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function getLayerType(): string
    {
        return 'provider';
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        $langKey    = 'leaflet_provider.' . $row['tile_provider'] . '.0';
        $translated = $translator->trans($langKey, [], 'contao_leaflet');

        if ($translated !== $langKey) {
            $provider = $translated;
        } else {
            $provider = $row['tile_provider'];
        }

        $label .= sprintf('<span class="tl_gray"> (%s)</span>', $provider);

        return $label;
    }
}
