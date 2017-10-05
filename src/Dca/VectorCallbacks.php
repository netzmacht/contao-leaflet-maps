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

namespace Netzmacht\Contao\Leaflet\Dca;

use Netzmacht\Contao\Toolkit\Dca\Callback\Callbacks;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\StyleModel;

/**
 * Helper class for the tl_leaflet_vector dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class VectorCallbacks extends Callbacks
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_vector';

    /**
     * Helper service name.
     *
     * @var string
     */
    protected static $serviceName = 'leaflet.dca.vector-callbacks';

    /**
     * Generate the row label.
     *
     * @param array $row Current data row.
     *
     * @return string
     */
    public function generateRow($row)
    {
        return sprintf(
            '%s <span class="tl_gray">(%s)</span>',
            $row['title'],
            $this->getFormatter()->formatValue('type', $row['type'])
        );
    }

    /**
     * Get all styles.
     *
     * @return array
     */
    public function getStyles()
    {
        $collection = StyleModel::findAll(array('order' => 'title'));

        return OptionsBuilder::fromCollection($collection, 'title')->getOptions();
    }
}
