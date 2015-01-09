<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;


use Netzmacht\Contao\DevTools\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\StyleModel;

class Vector
{
    public function generateRow($row)
    {
        return sprintf('%s <span class="tl_gray">[%s]</span>', $row['title'], $row['type']);
    }

    /**
     * Get all styles.
     *
     * @return array
     */
    public function getStyles()
    {
        $collection = StyleModel::findAll(array('order' => 'title'));

        return OptionsBuilder::fromCollection($collection, 'id', 'title')->getOptions();
    }
}
