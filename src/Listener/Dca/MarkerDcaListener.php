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

use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
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
     * Backend user.
     *
     * @var BackendUser
     */
    private $backendUser;

    /**
     * MarkerDcaListener constructor.
     *
     * @param Connection        $connection        Database connection.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param BackendUser       $backendUser       Backend user.
     */
    public function __construct(Connection $connection, RepositoryManager $repositoryManager, BackendUser $backendUser)
    {
        $this->connection        = $connection;
        $this->repositoryManager = $repositoryManager;
        $this->backendUser       = $backendUser;
    }

    /**
     * Check permissions.
     *
     * @return void
     *
     * @throws AccessDeniedException When permission is not granted.
     */
    public function checkPermissions(): void
    {
        if (!$this->backendUser->hasAccess(LayerModel::PERMISSION_DATA, 'leaflet_layer_permissions')) {
            throw new AccessDeniedException(
                sprintf('User "%s" not allowed to access layer data.', $this->backendUser->id)
            );
        }

        $layerId = $this->determineLayerId();
        if ($layerId && !$this->backendUser->hasAccess($layerId, 'leaflet_layers')) {
            throw new AccessDeniedException(
                sprintf('User "%s" not allowed to access layer "%s"', $this->backendUser->id, $layerId)
            );
        }
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

        $values = StringUtil::trimsplit(',', $value);
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

    /**
     * Determine the layer id.
     *
     * @return int|null
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function determineLayerId(): ?int
    {
        // Check the current action
        switch (Input::get('act')) {
            case 'paste':
                return null;

            case '':
            case 'create':
            case 'select':
                return (int) Input::get('id');

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                return (int) Input::get('pid');

            default:
                return (int) CURRENT_ID;
        }
    }
}
