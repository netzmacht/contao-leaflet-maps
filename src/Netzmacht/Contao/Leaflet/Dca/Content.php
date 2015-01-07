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

use Netzmacht\Contao\Leaflet\Model\MapModel;

/**
 * Class Content
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Content
{
    /**
     * Get all leaflet maps.
     *
     * @return array
     */
    public function getMaps()
    {
        $options    = array();
        $collection = MapModel::findAll();

        if ($collection) {
            foreach ($collection as $map) {
                $options[$map->id] = $map->title;
            }
        }

        return $options;
    }

    public function getEditMapLink($dataContainer)
    {
        if ($dataContainer->value < 1) {
            return '';
        }

        return sprintf(
            '<a href="%s%s&amp;popup=1&amp;rt=%s" %s>%s</a>',
            'contao/main.php?do=leaflet&amp;table=tl_leaflet_map&amp;act=edit&amp;id=',
            $dataContainer->value,
            \RequestToken::get(),
            sprintf(
                'title="%s" style="padding-left: 3px" '
                 . 'onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s\',\'url\':this.href});return false"',
                specialchars(sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dataContainer->value)),
                specialchars(
                    str_replace(
                        "'",
                        "\\'",
                        sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dataContainer->value)
                    )
                )
            ),
            \Image::getHtml(
                'alias.gif',
                $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"'
            )
        );
    }
}
