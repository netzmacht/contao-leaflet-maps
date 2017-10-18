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

use Contao\StringUtil;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class OverpassLabelRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
final class OverpassLabelRenderer extends AbstractLabelRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function getLayerType(): string
    {
        return 'overpass';
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        if ($row['overpassQuery']) {
            $label .= '<span class="tl_gray"> ' . StringUtil::substr($row['overpassQuery'], 50) . '</span>';
        }

        return $label;
    }
}
