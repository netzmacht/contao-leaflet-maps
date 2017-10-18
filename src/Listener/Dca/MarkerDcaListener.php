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

use Contao\Controller;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;

/**
 * Class Marker is the dca helper class for the tl_leaflet_marker dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class MarkerDcaListener
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * MarkerDcaListener constructor.
     *
     * @param Connection        $connection        Database connection.
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(Connection $connection, RepositoryManager $repositoryManager)
    {
        $this->connection        = $connection;
        $this->repositoryManager = $repositoryManager;
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
        return $row['title'];
    }

    /**
     * Get all icons.
     *
     * @return array
     */
    public function getIcons()
    {
        $repository = $this->repositoryManager->getRepository(IconModel::class);
        $collection = $repository->findAll(['order' => 'title']);
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
        $repository = $this->repositoryManager->getRepository(PopupModel::class);
        $collection = $repository->findAll(['order' => 'title']);
        $builder    = OptionsBuilder::fromCollection($collection, 'title');

        return $builder->getOptions();
    }

    /**
     * Save the coordinates.
     *
     * @param string         $value         The raw data.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return string
     */
    public function saveCoordinates($value, $dataContainer)
    {
        $combined = [
            'latitude'  => null,
            'longitude' => null,
            'altitude'  => null,
        ];

        $values = trimsplit(',', $value);
        $keys   = array_keys($combined);
        $count  = count($values);

        if ($count >= 2 && $count <= 3) {
            for ($i = 0; $i < $count; $i++) {
                $combined[$keys[$i]] = $values[$i];
            }
        }

        $this->connection->update('tl_leaflet_marker', $combined, ['id' => $dataContainer->id]);

        return null;
    }

    /**
     * Load the coordinates.
     *
     * @param string         $value         The raw data.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadCoordinates($value, $dataContainer)
    {
        $query     = 'SELECT latitude, longitude, altitude FROM tl_leaflet_marker WHERE id=:id';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('id', $dataContainer->id);

        $statement->execute();

        if ($row = $statement->fetch()) {
            $buffer = $row['latitude'];

            if ($buffer && $row['longitude']) {
                $buffer .= ',' . $row['longitude'];
            } else {
                return $buffer;
            }

            if ($buffer && $row['altitude']) {
                $buffer .= ',' . $row['altitude'];
            }

            return $buffer;
        }

        return '';
    }
}
