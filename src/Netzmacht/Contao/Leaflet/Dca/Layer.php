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


class Layer
{
    private $layers;

    public function __construct()
    {
        $this->layers = &$GLOBALS['LEAFLET_LAYERS'];
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
}
