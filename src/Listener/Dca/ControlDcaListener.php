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

use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;

/**
 * Class Control is the helper for the tl_leaflet_control dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class ControlDcaListener extends AbstractListener
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_control';

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Control types.
     *
     * @var array
     */
    private $types;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Construct.
     *
     * @param Manager           $manager           Data container manager.
     * @param Connection        $connection        Database connection.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param array             $types             Control types.
     */
    public function __construct(
        Manager $manager,
        Connection $connection,
        RepositoryManager $repositoryManager,
        array $types
    ) {
        parent::__construct($manager);

        $this->connection        = $connection;
        $this->types             = $types;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Get control types.
     *
     * @return array
     */
    public function getControlTypes(): array
    {
        return $this->types;
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
        $repository = $this->repositoryManager->getRepository(LayerModel::class);
        $collection = $repository->findAll();

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
        $repository = $this->repositoryManager->getRepository(ControlModel::class);
        $collection = $repository->findBy(['type=?'], ['zoom'], ['order' => 'title']);

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
        $query     = 'SELECT lid As layer, mode FROM tl_leaflet_control_layer WHERE cid=:cid ORDER BY sorting';
        $statement = $this->connection->prepare($query);

        $statement->bindValue('cid', $dataContainer->id);
        $result = $statement->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * Save layer relations.
     *
     * @param mixed          $layers        The layer id values.
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return null
     */
    public function saveLayerRelations($layers, $dataContainer)
    {
        $new       = StringUtil::deserialize($layers, true);
        $values    = [];
        $query     = 'SELECT * FROM tl_leaflet_control_layer WHERE cid=:cid order BY sorting';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('cid', $dataContainer->id);
        $result = $statement->executeQuery();

        while ($row = $result->fetchAssociative()) {
            $values[$row['lid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $layer) {
            if (!isset($values[$layer['layer']])) {
                $data = [
                    'tstamp'  => time(),
                    'lid'     => $layer['layer'],
                    'cid'     => $dataContainer->id,
                    'mode'    => $layer['mode'],
                    'sorting' => $sorting,
                ];

                $this->connection->insert('tl_leaflet_control_layer', $data);
                $sorting += 128;
            } else {
                $this->connection->update(
                    'tl_leaflet_control_layer',
                    [
                        'tstamp'  => time(),
                        'sorting' => $sorting,
                        'mode'    => $layer['mode'],
                    ],
                    [
                        'id' => $values[$layer['layer']]['id'],
                    ]
                );

                $sorting += 128;
                unset($values[$layer['layer']]);
            }
        }

        $ids = array_map(
            function ($item) {
                return $item['id'];
            },
            $values
        );

        if ($ids) {
            $this->connection->executeUpdate(
                'DELETE FROM tl_leaflet_control_layer WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }
}
