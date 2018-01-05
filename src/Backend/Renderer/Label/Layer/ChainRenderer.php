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

use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class ChainRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
final class ChainRenderer implements LayerLabelRenderer
{
    /**
     * List of layer label renderer.
     *
     * @var array|LayerLabelRenderer[]
     */
    private $renderer;

    /**
     * ChainRenderer constructor.
     *
     * @param array|LayerLabelRenderer[] $renderer List of layer label renderer.
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(array $row): bool
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($row)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        foreach ($this->renderer as $renderer) {
            if ($renderer->supports($row)) {
                return $renderer->render($row, $label, $translator);
            }
        }

        return $label;
    }
}
