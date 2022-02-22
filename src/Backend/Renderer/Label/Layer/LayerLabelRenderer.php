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
 * Interface LayerLabelRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
interface LayerLabelRenderer
{
    /**
     * Check if current row is supported.
     *
     * @param array $row Current row.
     *
     * @return bool
     */
    public function supports(array $row): bool;

    /**
     * Render the backend label of an layer.
     *
     * @param array      $row        Current row.
     * @param string     $label      Default label.
     * @param Translator $translator The translator.
     *
     * @return string
     */
    public function render(array $row, string $label, Translator $translator): string;
}
