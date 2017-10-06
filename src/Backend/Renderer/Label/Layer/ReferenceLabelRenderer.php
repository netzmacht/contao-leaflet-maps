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

use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class ReferenceLabelRenderer.
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
final class ReferenceLabelRenderer extends AbstractLabelRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function getLayerType(): string
    {
        return 'vectors';
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        $reference = LayerModel::findByPk($row['reference']);

        if ($reference) {
            $label .= '<span class="tl_gray"> (' . $reference->title . ')</span>';
        }

        return $label;
    }
}
