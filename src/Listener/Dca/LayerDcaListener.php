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

use Contao\Backend;
use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer\LayerLabelRenderer;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class Layer is the helper class for the tl_leaflet_layer dca.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class LayerDcaListener extends AbstractListener
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name = 'tl_leaflet_layer';

    /**
     * Layers definition.
     *
     * @var array
     */
    private $layers;

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
     * Tile providers configuration.
     *
     * @var array
     */
    private $tileProviders;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * OSM amenities.
     *
     * @var array
     */
    private $amenities;

    /**
     * Layer label renderer.
     *
     * @var LayerLabelRenderer
     */
    private $labelRenderer;

    /**
     * File formats.
     *
     * @var array
     */
    private $fileFormats;

    /**
     * Backend adapter.
     *
     * @var Backend|Adapter
     */
    private $backendAdapter;

    /**
     * Construct.
     *
     * @param Manager            $manager           Data container manager.
     * @param Connection         $connection        Database connection.
     * @param RepositoryManager  $repositoryManager Repository manager.
     * @param Translator         $translator        Translator.
     * @param LayerLabelRenderer $labelRenderer     Layer label renderer.
     * @param Adapter|Backend    $backendAdapter    Backend adapter.
     * @param array              $layers            Leaflet layer configuration.
     * @param array              $tileProviders     Tile providers.
     * @param array              $amenities         OSM amenities.
     * @param array              $fileFormats       File formats.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Manager $manager,
        Connection $connection,
        RepositoryManager $repositoryManager,
        Translator $translator,
        LayerLabelRenderer $labelRenderer,
        $backendAdapter,
        array $layers,
        array $tileProviders,
        array $amenities,
        array $fileFormats
    ) {
        parent::__construct($manager);

        $this->connection        = $connection;
        $this->layers            = $layers;
        $this->tileProviders     = $tileProviders;
        $this->translator        = $translator;
        $this->amenities         = $amenities;
        $this->labelRenderer     = $labelRenderer;
        $this->fileFormats       = $fileFormats;
        $this->repositoryManager = $repositoryManager;
        $this->backendAdapter    = $backendAdapter;
    }

    /**
     * Get layer options.
     *
     * @return array
     */
    public function getLayerOptions(): array
    {
        return array_keys($this->layers);
    }

    /**
     * Get tile provider options.
     *
     * @return array
     */
    public function getProviderOptions(): array
    {
        return array_keys($this->tileProviders);
    }

    /**
     * Get variants of the tile provider.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     *
     * @return array
     */
    public function getVariants($dataContainer)
    {
        if ($dataContainer->activeRecord
            && $dataContainer->activeRecord->tile_provider
            && !empty($this->tileProviders[$dataContainer->activeRecord->tile_provider]['variants'])
        ) {
            return $this->tileProviders[$dataContainer->activeRecord->tile_provider]['variants'];
        }

        return [];
    }

    /**
     * Generate a row.
     *
     * @param array  $row   The data row.
     * @param string $label Current row label.
     *
     * @return string
     */
    public function generateRow($row, $label)
    {
        if (!empty($this->layers[$row['type']]['icon'])) {
            $src = $this->layers[$row['type']]['icon'];
        } else {
            $src = 'iconPLAIN.svg';
        }

        $activeIcon   = $src;
        $disabledIcon = preg_replace('/(\.[^\.]+)$/', '_1$1', $src);

        if (!$row['active']) {
            $src = $disabledIcon;
        }

        $alt        = $this->getFormatter()->formatValue('type', $row['type']);
        $attributes = sprintf(
            'class="list-icon" title="%s" data-icon="%s" data-icon-disabled="%s"',
            StringUtil::specialchars(strip_tags($alt)),
            $activeIcon,
            $disabledIcon
        );

        $icon  = Image::getHtml($src, $alt, $attributes);
        $label = $this->labelRenderer->render($row, $label, $this->translator);

        return $icon . ' ' . $label;
    }

    /**
     * Get all marker cluster layers.
     *
     * @return array
     */
    public function getMarkerClusterLayers()
    {
        $types = array_keys(
            array_filter(
                $this->layers,
                function ($item) {
                    return !empty($item['markerCluster']);
                }
            )
        );

        $repository = $this->repositoryManager->getRepository(LayerModel::class);
        $collection = $repository->findMultipleByTypes($types);
        $builder    = OptionsBuilder::fromCollection(
            $collection,
            'id',
            function ($row) {
                return sprintf('%s [%s]', $row['title'], $row['type']);
            }
        );

        return $builder->getOptions();
    }

    /**
     * Get the paste buttons depending on the layer type.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     * @param array          $row           The data row.
     * @param string         $table         The table name.
     * @param null           $whatever      Who knows what the purpose of this var is.
     * @param array          $children      The child content.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getPasteButtons($dataContainer, $row, $table, $whatever, $children)
    {
        $pasteAfterUrl = $this->backendAdapter->addToUrl(
            'act=' . $children['mode'] . '&amp;mode=1&amp;pid=' . $row['id']
            . (!is_array($children['id']) ? '&amp;id=' . $children['id'] : '')
        );

        $buffer = sprintf(
            '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
            $pasteAfterUrl,
            StringUtil::specialchars($this->translator->trans('pasteafter.1', [$row['id']], 'contao_' . $table)),
            Image::getHtml(
                'pasteafter.svg',
                $this->translator->trans('pasteafter.1', [$row['id']], 'contao_' . $table)
            )
        );

        if (isset($row['type']) && !empty($this->layers[$row['type']]['children'])) {
            $pasteIntoUrl = $this->backendAdapter->addToUrl(
                sprintf(
                    'act=%s&amp;mode=2&amp;pid=%s%s',
                    $children['mode'],
                    $row['id'],
                    !is_array($children['id']) ? '&amp;id=' . $children['id'] : ''
                )
            );

            $buffer .= sprintf(
                '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
                $pasteIntoUrl,
                StringUtil::specialchars($this->translator->trans('pasteinto.1', [$row['id']], 'contao_' . $table)),
                Image::getHtml(
                    'pasteinto.svg',
                    $this->translator->trans('pasteinto.1', [$row['id']], 'contao_' . $table)
                )
            );
        } elseif ($row['id'] > 0) {
            $buffer .= Image::getHtml('pasteinto_.svg');
        }

        return $buffer;
    }

    /**
     * Generate the markers button.
     *
     * @param array  $row        Current row.
     * @param string $href       The button href.
     * @param string $label      The button label.
     * @param string $title      The button title.
     * @param string $icon       The button icon.
     * @param string $attributes Optional attributes.
     *
     * @return string
     */
    public function generateMarkersButton($row, $href, $label, $title, $icon, $attributes)
    {
        if (empty($this->layers[$row['type']]['markers'])) {
            return '';
        }

        return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Generate the vectors button.
     *
     * @param array  $row        Current row.
     * @param string $href       The button href.
     * @param string $label      The button label.
     * @param string $title      The button title.
     * @param string $icon       The button icon.
     * @param string $attributes Optional attributes.
     *
     * @return string
     */
    public function generateVectorsButton($row, $href, $label, $title, $icon, $attributes)
    {
        if (empty($this->layers[$row['type']]['vectors'])) {
            return '';
        }

        return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Delete the relations when the layer is deleted.
     *
     * @param \DataContainer $dataContainer The dataContainer driver.
     * @param int            $undoId        The id of the undo entry.
     *
     * @return void
     */
    public function deleteRelations($dataContainer, $undoId)
    {
        if ($undoId) {
            $statement = $this->connection->prepare('SELECT * FROM tl_undo WHERE id=:id LIMIT 0,1');
            $statement->bindValue('id', $undoId);
            $result = $statement->executeQuery();

            $undo = $result->fetchAssociative();

            $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_map_layer WHERE lid=:lid');
            $statement->bindValue('lid', $dataContainer->id);
            $result = $statement->executeQuery();

            $undo['data'] = StringUtil::deserialize($undo['data'], true);

            while ($row = $result->fetchAssociative()) {
                $undo['data']['tl_leaflet_map_layer'][] = $row;
            }

            $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_control_layer WHERE lid=:lid');
            $statement->bindValue('lid', $dataContainer->id);
            $result = $statement->executeQuery();

            $undo['data']['tl_leaflet_control_layer'] = $result->fetchAllAssociative();

            $this->connection->update('tl_undo', ['data' => $undo['data']], ['id' => $undo['id']]);
        }

        $this->connection->delete('tl_leaflet_map_layer', ['lid' => $dataContainer->id]);
        $this->connection->delete('tl_leaflet_control_layer', ['lid' => $dataContainer->id]);
    }

    /**
     * Get bounds modes supported by the layer.
     *
     * @param \DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function getBoundsModes($dataContainer)
    {
        $options = [];

        if ($dataContainer->activeRecord && !empty($this->layers[$dataContainer->activeRecord->type]['boundsMode'])) {
            foreach ($this->layers[$dataContainer->activeRecord->type]['boundsMode'] as $mode => $enabled) {
                if ($enabled === true) {
                    $options[] = $mode;
                } elseif ($enabled === 'deferred' && $dataContainer->activeRecord->deferred) {
                    $options[] = $mode;
                }
            }
        }

        return $options;
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
        $repository = $this->repositoryManager->getRepository(LayerModel::class);
        $collection = $repository->findBy(['tl_leaflet_layer.id!=?'], [$dataContainer->id]);

        return OptionsBuilder::fromCollection($collection, 'title')
            ->asTree()
            ->getOptions();
    }

    /**
     * Get all know osm amenities as options.
     *
     * @return array
     */
    public function getAmenities()
    {
        return $this->amenities;
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
     * Get the file formats.
     *
     * @return array
     */
    public function getFileFormats(): array
    {
        return array_keys($this->fileFormats);
    }

    /**
     * Prepare the file widget.
     *
     * @param mixed         $value         Given value.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed
     */
    public function prepareFileWidget($value, $dataContainer)
    {
        if ($dataContainer->activeRecord) {
            $fileFormat = $dataContainer->activeRecord->fileFormat;

            if (isset($this->fileFormats[$fileFormat])) {
                $definition = $this->getDefinition();
                $definition->set(
                    ['fields', $dataContainer->field, 'eval', 'extensions'],
                    implode(',', $this->fileFormats[$fileFormat])
                );

                $definition->set(
                    ['fields', $dataContainer->field, 'label', 1],
                    sprintf(
                        $definition->get(['fields', $dataContainer->field, 'label', 1]),
                        implode(', ', $this->fileFormats[$fileFormat])
                    )
                );
            }
        }

        return $value;
    }

    /**
     * Generate a button.
     *
     * @param array  $row        Current row.
     * @param string $href       The button href.
     * @param string $label      The button label.
     * @param string $title      The button title.
     * @param string $icon       The button icon.
     * @param string $attributes Optional attributes.
     *
     * @return string
     */
    protected function generateButton($row, $href, $label, $title, $icon, $attributes)
    {
        return sprintf(
            '<a href="%s" title="%s">%s</a> ',
            Backend::addToUrl($href . '&amp;id=' . $row['id']),
            $title,
            Image::getHtml($icon, $label, $attributes)
        );
    }
}
