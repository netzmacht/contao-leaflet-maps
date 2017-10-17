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

use Contao\FilesModel;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class FileLabelRenderer
 *
 * @package Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer
 */
class FileLabelRenderer extends AbstractLabelRenderer
{
    /**
     * {@inheritDoc}
     */
    protected function getLayerType(): string
    {
        return 'file';
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $row, string $label, Translator $translator): string
    {
        $file = FilesModel::findByPk($row['file']);

        if ($file) {
            $label .= ' <span class="tl_gray">(' . $file->path . ')</span>';
        }

        return $label;
    }
}
