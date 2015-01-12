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
use Netzmacht\Contao\Leaflet\Model\IconModel;

/**
 * Class Marker is the dca helper class for the tl_leaflet_marker dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Marker
{
    /**
     * Generate the row label.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow($row)
    {
        return $row['title'];
    }

    /**
     * Get all icons.
     *
     * @return array
     */
    public function getIcons()
    {
        $collection = IconModel::findAll(array('order' => 'title'));
        $builder    = OptionsBuilder::fromCollection(
            $collection,
            'id',
            function ($model) {
                return sprintf('%s [%s]', $model['title'], $model['type']);
            }
        );

        return $builder->getOptions();
    }
}
