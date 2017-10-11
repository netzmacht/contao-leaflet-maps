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

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\StyleModel;

/**
 * Helper class for the tl_leaflet_vector dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class VectorDcaListener extends AbstractListener
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_vector';

    /**
     * Vector options.
     *
     * @var array
     */
    private $vectors;

    /**
     * Construct.
     *
     * @param Manager $dcaManager Data container manager.
     * @param array   $vectors    Vectors.
     */
    public function __construct(Manager $dcaManager, array $vectors)
    {
        parent::__construct($dcaManager);

        $this->vectors = $vectors;
    }

    /**
     * Get the vector options.
     *
     * @return array
     */
    public function getVectorOptions(): array
    {
        return $this->vectors;
    }

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
            function ($model) {
                return sprintf('%s [%s]', $model['title'], $model['type']);
            }
        );

        return $builder->getOptions();
    }

    /**
     * Get all popups.
     *
     * @return array
     */
    public function getPopups()
    {
        $collection = PopupModel::findAll(array('order' => 'title'));
        $builder    = OptionsBuilder::fromCollection($collection, 'title');

        return $builder->getOptions();
    }
}
