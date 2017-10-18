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
     * Construct.
     *
     * @param Manager           $dcaManager        Data container manager.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param array             $vectors           Vectors.
     */
    public function __construct(Manager $dcaManager, RepositoryManager $repositoryManager, array $vectors)
    {
        parent::__construct($dcaManager);

        $this->vectors           = $vectors;
        $this->repositoryManager = $repositoryManager;
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
}
