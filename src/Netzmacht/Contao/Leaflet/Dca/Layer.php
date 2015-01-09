<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;


use Netzmacht\Contao\DevTools\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\LayerModel;

class Layer
{
    private $layers;

    public function __construct()
    {
        $this->layers = &$GLOBALS['LEAFLET_LAYERS'];

        \Controller::loadLanguageFile('leaflet_layer');
    }

    public function getVariants($dataContainer)
    {
        if ($dataContainer->activeRecord
            && $dataContainer->activeRecord->tile_provider
            && !empty($GLOBALS['LEAFLET_TILE_PROVIDERS'][$dataContainer->activeRecord->tile_provider]['variants'])
        ) {
            return $GLOBALS['LEAFLET_TILE_PROVIDERS'][$dataContainer->activeRecord->tile_provider]['variants'];
        }

        return array();
    }

    public function generateRow($row, $label)
    {
        $alt = empty($GLOBALS['TL_LANG']['leaflet_layer'][$row['type']][0])
            ? $row['type']
            : $GLOBALS['TL_LANG']['leaflet_layer'][$row['type']][0];

        if (!empty($this->layers[$row['type']]['icon'])) {
            $src = $this->layers[$row['type']]['icon'];

        } else {
            $src = 'iconPLAIN.gif';
        }

        if (!$row['active']) {
            $src = preg_replace('/(\.[^\.]+)$/', '_1$1', $src);
        }

        $icon = \Image::getHtml($src, $alt, sprintf('title="%s"', strip_tags($alt)));

        if (!empty($this->layers[$row['type']]['label'])) {
            $label = $this->layers[$row['type']]['label']($row, $label);
        }

        return $icon . ' ' . $label;
    }

    public function getMarkerClusterLayers()
    {
        $types = array_keys(
            array_filter(
                $GLOBALS['LEAFLET_LAYERS'],
                function ($item) {
                    return !empty($item['markerCluster']);
                }
            )
        );

        $collection = LayerModel::findMultipleByTypes($types);
        $builder    = OptionsBuilder::fromCollection(
            $collection,
            'id',
            function($row) {
                return sprintf('%s [%s]', $row['title'], $row['type']);
            }
        );

        return $builder->getOptions();
    }

    // Call paste_button_callback (&$dc, $row, $table, $cr, $childs, $previous, $next)
    public function getPasteButtons($dataContainer, $row, $table, $whatever, $children)
    {
        $pasteAfterUrl = \Controller::addToUrl(
            'act='.$children['mode'].'&amp;mode=1&amp;pid='.$row['id']
            .(!is_array($children['id']) ? '&amp;id='.$children['id'] : '')
        );

        $buffer = sprintf(
            '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
            $pasteAfterUrl,
            specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id'])),
            \Image::getHtml(
                'pasteafter.gif',
                sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id'])
            )
        );

        if (!empty($this->layers[$row['type']]['children'])) {
            $pasteIntoUrl = \Controller::addToUrl(
                sprintf(
                    'act=%s&amp;mode=2&amp;pid=%s%s',
                    $children['mode'],
                    $row['id'],
                    !is_array($children['id']) ? '&amp;id='.$children['id'] : ''
                )
            );

            $buffer .= sprintf(
                '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
                $pasteIntoUrl,
                specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id'])),
                \Image::getHtml(
                    'pasteinto.gif',
                    sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id'])
                )
            );

        } elseif ($row['id'] > 0) {
            $buffer .= \Image::getHtml('pasteinto_.gif');
        }

        return $buffer;
    }

    public function generateMarkersButton($row, $href, $label, $title, $icon, $attributes)
    {
        if (empty($this->layers[$row['type']]['markers'])) {
            return '';
        }

        return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
    }

    public function generateVectorsButton($row, $href, $label, $title, $icon, $attributes)
    {
        if (empty($this->layers[$row['type']]['vectors'])) {
            return '';
        }

        return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
    }

    public function getLayers($dataContainer)
    {
        $collection = LayerModel::findBy('id !', $dataContainer->id);

        return OptionsBuilder::fromCollection($collection, 'id', 'title')
            ->asTree()
            ->getOptions();
    }

    /**
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @param $attributes
     *
     * @return string
     */
    protected function generateButton($row, $href, $label, $title, $icon, $attributes)
    {
        return sprintf(
            '<a href="%s" title="%s">%s</a> ',
            \Backend::addToUrl($href . '&amp;id=' . $row['id']),
            $title,
            \Image::getHtml($icon, $label, $attributes)
        );
    }
}
