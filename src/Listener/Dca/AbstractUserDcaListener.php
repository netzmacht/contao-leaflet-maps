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

use Contao\Image;
use Contao\StringUtil;
use Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer\LayerLabelRenderer;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class AbstractUserDcaListener
 */
abstract class AbstractUserDcaListener extends AbstractListener
{
    /**
     * Layer label renderer.
     *
     * @var LayerLabelRenderer
     */
    private $labelRenderer;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Layers definitions.
     *
     * @var array
     */
    private $layers;

    /**
     * Constructor.
     *
     * @param Manager            $dcaManager    Dca manager.
     * @param LayerLabelRenderer $labelRenderer Layer label renderer.
     * @param Translator         $translator    Translator.
     * @param array              $layers        Layers definition.
     */
    public function __construct(
        Manager $dcaManager,
        LayerLabelRenderer $labelRenderer,
        Translator $translator,
        array $layers
    ) {
        parent::__construct($dcaManager);

        $this->labelRenderer = $labelRenderer;
        $this->translator    = $translator;
        $this->layers        = $layers;
    }

    /**
     * Generate the layers row label.
     *
     * @param array $row Layer data row.
     *
     * @return string
     */
    public function generateLayersRowLabel(array $row): string
    {
        if (!empty($this->layers[$row['type']]['icon'])) {
            $src = $this->layers[$row['type']]['icon'];
        } else {
            $src = 'iconPLAIN.gif';
        }

        $activeIcon   = $src;
        $disabledIcon = preg_replace('/(\.[^\.]+)$/', '_1$1', $src);

        if (!$row['active']) {
            $src = $disabledIcon;
        }

        $alt        = $this->getFormatter(LayerModel::getTable())->formatValue('type', $row['type']);
        $attributes = sprintf(
            'class="list-icon" title="%s" data-icon="%s" data-icon-disabled="%s"',
            StringUtil::specialchars(strip_tags($alt)),
            $activeIcon,
            $disabledIcon
        );

        $label  = $this->getFormatter(LayerModel::getTable())->formatValue('title', $row['title']);
        $label .= sprintf(' <span class="tl_gray">[ID %s]</span>', $row['id']);
        $icon   = Image::getHtml($src, $alt, $attributes);
        $label  = $this->labelRenderer->render($row, $label, $this->translator);

        return $icon . ' ' . $label;
    }
}
