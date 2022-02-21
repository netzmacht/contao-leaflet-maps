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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class Map is the helper class for the tl_leaflet_map dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class MapDcaListener extends AbstractListener
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_map';

    /**
     * The database connection.
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
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Session.
     *
     * @var Session
     */
    private $session;

    /**
     * Construct.
     *
     * @param Manager           $manager           Data container manager.
     * @param Connection        $connection        Database connection.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Translator        $translator        Translator.
     * @param Session           $session           Session.
     */
    public function __construct(
        Manager $manager,
        Connection $connection,
        RepositoryManager $repositoryManager,
        Translator $translator,
        Session $session
    ) {
        parent::__construct($manager);

        $this->connection        = $connection;
        $this->repositoryManager = $repositoryManager;
        $this->translator        = $translator;
        $this->session           = $session;
    }

    /**
     * Add warnings for incomplete configurations.
     *
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return void
     */
    public function addIncompleteConfigurationWarning($dataContainer): void
    {
        if (Input::get('act') !== 'edit') {
            return;
        }

        $repository = $this->repositoryManager->getRepository(MapModel::class);
        $map        = $repository->find((int) $dataContainer->id);

        if (!$map) {
            return;
        }

        if ($map->zoom === null || $map->zoom === '') {
            $this->session->getFlashBag()->add(
                'contao.BE.info',
                $this->translator->trans('ERR.leafletMissingZoomLevel', [], 'contao_default')
            );
        }
    }

    /**
     * Load layer relations.
     *
     * @param mixed         $value         The actual value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadLayerRelations($value, $dataContainer): array
    {
        $statement = $this->connection->prepare('SELECT lid FROM tl_leaflet_map_layer WHERE mid=:mid ORDER BY sorting');
        $statement->bindValue('mid', $dataContainer->id);
        $result = $statement->executeQuery();

        return $result->fetchFirstColumn();
    }

    /**
     * Save layer relations.
     *
     * @param mixed         $layerId       The layer id values.
     * @param DataContainer $dataContainer The dataContainer driver.
     *
     * @return null
     */
    public function saveLayerRelations($layerId, $dataContainer)
    {
        $new       = array_filter(StringUtil::deserialize($layerId, true));
        $values    = [];
        $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_map_layer WHERE mid=:mid order BY sorting');

        $statement->bindValue('mid', $dataContainer->id);
        $result = $statement->executeQuery();

        while ($row = $result->fetchAssociative()) {
            $values[$row['lid']] = $row;
        }

        $sorting = 0;

        foreach ($new as $layerId) {
            if (!isset($values[$layerId])) {
                $data = [
                    'tstamp'  => time(),
                    'lid'     => $layerId,
                    'mid'     => $dataContainer->id,
                    'sorting' => $sorting,
                ];

                $this->connection->insert('tl_leaflet_map_layer', $data);
                $sorting += 128;
            } else {
                if ($values[$layerId]['sorting'] <= ($sorting - 128)
                    || $values[$layerId]['sorting'] >= ($sorting + 128)
                ) {
                    $this->connection->update(
                        'tl_leaflet_map_layer',
                        ['tstamp' => time(), 'sorting' => $sorting],
                        ['id' => $values[$layerId]['id']]
                    );
                }

                $sorting += 128;
                unset($values[$layerId]);
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
                'DELETE FROM tl_leaflet_map_layer WHERE id IN(?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );
        }

        return null;
    }

    /**
     * Get all layers except of the current layer.
     *
     * @return array
     */
    public function getLayers()
    {
        $repository = $this->repositoryManager->getRepository(LayerModel::class);
        $collection = $repository->findAll(['order' => 'title']);

        return OptionsBuilder::fromCollection($collection, 'title')
            ->asTree()
            ->getOptions();
    }

    /**
     * Copy layer relations if a map is duplicated.
     *
     * @param string|int    $insertId      Insert id of the new map.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return void
     */
    public function copyLayerRelations($insertId, DataContainer $dataContainer): void
    {
        $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_map_layer WHERE mid=:mid order BY sorting');
        $statement->bindValue('mid', $dataContainer->id);
        $result = $statement->executeQuery();

        while ($row = $result->fetchAssociative()) {
            unset($row['id']);
            $row['tstamp'] = time();
            $row['mid']    = $insertId;
            $this->connection->insert('tl_leaflet_map_layer', $row);
        }
    }
}
