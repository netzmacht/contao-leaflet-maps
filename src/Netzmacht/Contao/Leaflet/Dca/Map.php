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

use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Class Map is the helper class for the tl_leaflet_map dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Map
{
    use ServiceContainerTrait;

    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->database = static::getServiceContainer()->getDatabaseConnection();
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
}
