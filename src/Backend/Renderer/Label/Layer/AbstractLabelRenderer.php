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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer;

/**
 * Class AbstractLabelRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
abstract class AbstractLabelRenderer implements LayerLabelRenderer
{
    /**
     * {@inheritdoc}
     */
    public function supports(array $row): bool
    {
        return $row['type'] === $this->getLayerType();
    }

    /**
     * Get the supported layer type.
     *
     * @return string
     */
    abstract protected function getLayerType(): string;
}
