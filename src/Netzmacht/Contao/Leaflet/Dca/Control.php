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
use Netzmacht\Contao\DevTools\ServiceContainerTrait;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;

/**
 * Class Control is the helper for the tl_leaflet_control dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Control
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
        $this->database = static::getService('database.connection');
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
        $options    = array();
        $collection = LayerModel::findBy('pid', '0', array('order' => 'title'));

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = $model->title;
            }
        }

        return $options;
    }

    /**
     * Get the zoom controls for the reference value of the loading control.
     *
     * @return array
     */
    public function getZoomControls()
    {
        $collection = ControlModel::findBy('type', 'zoom', array('order' => 'title'));

        return OptionsBuilder::fromCollection($collection, 'id', 'title')->getOptions();
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
