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
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Leaflet\Model\StyleModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;

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
     * Construct.
     *
     * @param Manager           $dcaManager        Data container manager.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param BackendUser       $backendUser       Backend user.
     * @param array             $vectors           Vectors.
     */
    public function __construct(
        Manager $dcaManager,
        RepositoryManager $repositoryManager,
        BackendUser $backendUser,
        array $vectors
    ) {
        parent::__construct($dcaManager);

        $this->vectors           = $vectors;
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
        $repository = $this->repositoryManager->getRepository(StyleModel::class);
        $collection = $repository->findAll(['order' => 'title']);

        return OptionsBuilder::fromCollection($collection, 'title')->getOptions();
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
