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
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;

/**
 * Class Control is the helper for the tl_leaflet_control dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class ControlCallbacks extends Callbacks
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_control';

    /**
     * Helper service name.
     *
     * @var string
     */
    protected static $serviceName = 'leaflet.dca.control-callbacks';

    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Construct.
     *
     * @param Manager   $manager  Data container manager.
     * @param \Database $database Database connection.
     */
    public function __construct(Manager $manager, \Database $database)
    {
        parent::__construct($manager);

        $this->database = $database;
    }

    /**
     * Generate a row.
     *
     * @param array $row The data row.
     *
     * @return string
     */
    public function generateRow($row)
    {
        return sprintf(
            '%s <span class="tl_gray">[%s]</span>',
            $row['title'],
            $row['type']
        );
    }

    /**
     * Get layers for the layers control.
     *
     * @return array
     */
    public function getLayers()
    {
        $collection = LayerModel::findAll();

        return OptionsBuilder::fromCollection($collection, 'title')
            ->asTree()
            ->getOptions();
    }

    /**
     * Get the zoom controls for the reference value of the loading control.
     *
     * @return array
     */
    public function getZoomControls()
    {
        $collection = ControlModel::findBy('type', 'zoom', array('order' => 'title'));

        return OptionsBuilder::fromCollection($collection, 'title')->getOptions();
    }

    /**
     * Load layer relations.
     *
     * @param mixed          $value         The actual value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadLayerRelations($value, $dataContainer)
    {
        $result = $this->database
            ->prepare('SELECT lid As layer, mode FROM tl_leaflet_control_layer WHERE cid=? ORDER BY sorting')
            ->execute($dataContainer->id);

        return $result->fetchAllAssoc();
    }

    /**
     * Save layer relations.
     *
     * @param $layers        $layers        The layer id values.
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return null
     */
    public function saveLayerRelations($layers, $dataContainer)
    {
        $new    = deserialize($layers, true);
        $values = array();
        $result = $this->database
            ->prepare('SELECT * FROM tl_leaflet_control_layer WHERE cid=? order BY sorting')
            ->execute($dataContainer->id);

        while ($result->next()) {
            $values[$result->lid] = $result->row();
        }

        $sorting = 0;

        foreach ($new as $layer) {
            if (!isset($values[$layer['layer']])) {
                $this->database
                    ->prepare('INSERT INTO tl_leaflet_control_layer %s')
                    ->set(
                        array(
                            'tstamp'  => time(),
                            'lid'     => $layer['layer'],
                            'cid'     => $dataContainer->id,
                            'mode'    => $layer['mode'],
                            'sorting' => $sorting
                        )
                    )
                    ->execute();

                $sorting += 128;
            } else {
                $this->database
                    ->prepare('UPDATE tl_leaflet_control_layer %s WHERE id=?')
                    ->set(
                        array(
                            'tstamp'  => time(),
                            'sorting' => $sorting,
                            'mode'    => $layer['mode']
                        )
                    )
                    ->execute($values[$layer['layer']]['id']);

                $sorting += 128;
                unset ($values[$layer['layer']]);
            }
        }

        $ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $this->database->query('DELETE FROM tl_leaflet_control_layer WHERE id IN(' . implode(',', $ids) . ')');
        }

        return null;
    }
}
