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

use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Dca\Callback\Callbacks;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;

/**
 * Class Map is the helper class for the tl_leaflet_map dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class MapCallbacks extends Callbacks
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_map';

    /**
     * Helper service name.
     *
     * @var string
     */
    protected static $serviceName = 'leaflet.dca.map-callbacks';

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
            ->prepare('SELECT lid FROM tl_leaflet_map_layer WHERE mid=? ORDER BY sorting')
            ->execute($dataContainer->id);

        return $result->fetchEach('lid');
    }

    /**
     * Save layer relations.
     *
     * @param mixed          $layerId       The layer id values.
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return null
     */
    public function saveLayerRelations($layerId, $dataContainer)
    {
        $new    = deserialize($layerId, true);
        $values = array();
        $result = $this->database
            ->prepare('SELECT * FROM tl_leaflet_map_layer WHERE mid=? order BY sorting')
            ->execute($dataContainer->id);

        while ($result->next()) {
            $values[$result->lid] = $result->row();
        }

        $sorting = 0;

        foreach ($new as $layerId) {
            if (!isset($values[$layerId])) {
                $this->database
                    ->prepare('INSERT INTO tl_leaflet_map_layer %s')
                    ->set(
                        array(
                            'tstamp'  => time(),
                            'lid'     => $layerId,
                            'mid'     => $dataContainer->id,
                            'sorting' => $sorting
                        )
                    )
                    ->execute();

                $sorting += 128;
            } else {
                if ($values[$layerId]['sorting'] <= ($sorting - 128)
                    || $values[$layerId]['sorting'] >= ($sorting + 128)) {
                    $this->database
                        ->prepare('UPDATE tl_leaflet_map_layer %s WHERE id=?')
                        ->set(array('tstamp'  => time(), 'sorting' => $sorting))
                        ->execute($values[$layerId]['id']);
                }

                $sorting += 128;
                unset ($values[$layerId]);
            }
        }

        $ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $this->database->query('DELETE FROM tl_leaflet_map_layer WHERE id IN(' . implode(',', $ids) . ')');
        }

        return null;
    }

    /**
     * Get all layers except of the current layer.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return array
     */
    public function getLayers($dataContainer)
    {
        $collection = LayerModel::findBy('id !', $dataContainer->id);

        return OptionsBuilder::fromCollection($collection, 'title')
            ->asTree()
            ->getOptions();
    }
}
