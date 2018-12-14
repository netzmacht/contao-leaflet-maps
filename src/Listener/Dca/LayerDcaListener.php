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
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Framework\Adapter;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer\LayerLabelRenderer;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Listener\Button\StateButtonCallbackListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class Layer is the helper class for the tl_leaflet_layer dca.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
     * Backend user.
     *
     * @var BackendUser
     */
    private $backendUser;

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
     * State button callback listener.
     *
     * @var StateButtonCallbackListener
     */
    private $stateButtonCallbackListener;

    /**
     * Construct.
     *
     * @param Manager                     $manager                     Data container manager.
     * @param Connection                  $connection                  Database connection.
     * @param RepositoryManager           $repositoryManager           Repository manager.
     * @param Translator                  $translator                  Translator.
     * @param LayerLabelRenderer          $labelRenderer               Layer label renderer.
     * @param BackendUser                 $backendUser                 Backend user.
     * @param StateButtonCallbackListener $stateButtonCallbackListener State button callback listener.
     * @param Adapter|Backend             $backendAdapter              Backend adapter.
     * @param array                       $layers                      Leaflet layer configuration.
     * @param array                       $tileProviders               Tile providers.
     * @param array                       $amenities                   OSM amenities.
     * @param array                       $fileFormats                 File formats.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Manager $manager,
        Connection $connection,
        RepositoryManager $repositoryManager,
        Translator $translator,
        LayerLabelRenderer $labelRenderer,
        BackendUser $backendUser,
        StateButtonCallbackListener $stateButtonCallbackListener,
        $backendAdapter,
        array $layers,
        array $tileProviders,
        array $amenities,
        array $fileFormats
    ) {
        parent::__construct($manager);

        $this->connection                  = $connection;
        $this->layers                      = $layers;
        $this->tileProviders               = $tileProviders;
        $this->translator                  = $translator;
        $this->amenities                   = $amenities;
        $this->labelRenderer               = $labelRenderer;
        $this->fileFormats                 = $fileFormats;
        $this->repositoryManager           = $repositoryManager;
        $this->backendAdapter              = $backendAdapter;
        $this->backendUser                 = $backendUser;
        $this->stateButtonCallbackListener = $stateButtonCallbackListener;
    }

    /**
     * Check the permissions.
     *
     * @param DataContainer $dataContainer Data container.
     *
     * @return void
     *
     * @throws AccessDeniedException If permissions are not granted.
     */
    public function checkPermissions(DataContainer $dataContainer): void
    {
        if ($this->backendUser->isAdmin) {
            return;
        }

        $action     = Input::get('act');
        $permission = $this->determinePermission($action);

        if ($permission && !$this->backendUser->hasAccess($permission, 'leaflet_layer_permissions')) {
            throw new AccessDeniedException(
                sprintf('Permission "%s" not granted to access layer "%s"', $permission, $dataContainer->id)
            );
        }

        $this->getDefinition()->set(['list', 'sorting', 'root'], $this->backendUser->leaflet_layers);

        $layerId = $this->determineLayerId();
        if ($layerId && !$this->backendUser->hasAccess($layerId, 'leaflet_layers')) {
            throw new AccessDeniedException(
                sprintf('User "%s" not allowed to access layer "%s"', $this->backendUser->id, $layerId)
            );
        }

        if (!$this->backendUser->hasAccess(LayerModel::PERMISSION_CREATE, 'leaflet_layer_permissions')) {
            $this->getDefinition()->set(['config', 'closed'], true);
        }
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
            $src = 'iconPLAIN.gif';
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
                'pasteafter.gif',
                $this->translator->trans('pasteafter.1', [$row['id']], 'contao_' . $table)
            )
        );

        if (!empty($this->layers[$row['type']]['children'])) {
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
                    'pasteinto.gif',
                    $this->translator->trans('pasteinto.1', [$row['id']], 'contao_' . $table)
                )
            );
        } elseif ($row['id'] > 0) {
            $buffer .= Image::getHtml('pasteinto_.gif');
        }

        return $buffer;
    }

    /**
     * Generate the edit button.
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
    public function generateEditButton($row, $href, $label, $title, $icon, $attributes): string
    {
        if ($this->backendUser->hasAccess('edit', 'leaflet_layer_permissions')) {
            return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
        }

        return '';
    }

    /**
     * Generate the cut button.
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
    public function generateCutButton($row, $href, $label, $title, $icon, $attributes): string
    {
        if ($this->backendUser->hasAccess('edit', 'leaflet_layer_permissions')) {
            return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
        }

        return '';
    }

    /**
     * Generate the copy button.
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
    public function generateCopyButton($row, $href, $label, $title, $icon, $attributes): string
    {
        if ($this->backendUser->hasAccess('create', 'leaflet_layer_permissions')) {
            return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
        }

        return '';
    }

    /**
     * Generate the toggle button.
     *
     * @param array         $row               Current data row.
     * @param string|null   $href              Button link.
     * @param string|null   $label             Button label.
     * @param string|null   $title             Button title.
     * @param string|null   $icon              Enabled button icon.
     * @param string|null   $attributes        Html attributes as string.
     * @param string        $tableName         Table name.
     * @param array|null    $rootIds           Root ids.
     * @param array|null    $childRecordIds    Child record ids.
     * @param bool          $circularReference Circular reference flag.
     * @param string|null   $previous          Previous button name.
     * @param string|null   $next              Next button name.
     * @param DataContainer $dataContainer     Data container driver.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function generateToggleButton(
        array $row,
        $href,
        $label,
        $title,
        $icon,
        $attributes,
        string $tableName,
        $rootIds,
        $childRecordIds,
        bool $circularReference,
        $previous,
        $next,
        $dataContainer
    ): string {
        if ($this->backendUser->hasAccess('edit', 'leaflet_layer_permissions')) {
            return $this->stateButtonCallbackListener->handleButtonCallback(
                $row,
                $href,
                $label,
                $title,
                $icon,
                $attributes,
                $tableName,
                $rootIds,
                $childRecordIds,
                $circularReference,
                $previous,
                $next,
                $dataContainer
            );
        }

        return '';
    }

    /**
     * Generate the edit button.
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
    public function generateDeleteButton($row, $href, $label, $title, $icon, $attributes): string
    {
        if ($this->backendUser->hasAccess('delete', 'leaflet_layer_permissions')) {
            return $this->generateButton($row, $href, $label, $title, $icon, $attributes);
        }

        return '';
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

        if (!$this->backendUser->hasAccess('data', 'leaflet_layer_permissions')) {
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

        if (!$this->backendUser->hasAccess('data', 'leaflet_layer_permissions')) {
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
            $statement->execute();

            $undo = $statement->fetch();

            $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_map_layer WHERE lid=:lid');
            $statement->bindValue('lid', $dataContainer->id);
            $statement->execute();

            $undo['data'] = StringUtil::deserialize($undo['data'], true);

            while ($row = $statement->fetch()) {
                $undo['data']['tl_leaflet_map_layer'][] = $row;
            }

            $statement = $this->connection->prepare('SELECT * FROM tl_leaflet_control_layer WHERE lid=:lid');
            $statement->bindValue('lid', $dataContainer->id);
            $statement->execute();

            $undo['data']['tl_leaflet_control_layer'] = $statement->fetchAll();

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

    /**
     * Determine permission for current action.
     *
     * @param string|null $action Given action.
     *
     * @return string|null
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function determinePermission(?string $action): ?string
    {
        $permission = null;

        switch ($action) {
            case 'edit':
            case 'toggle':
            case 'editAll':
            case 'overrideAll':
            case 'cutAll':
                return LayerModel::PERMISSION_EDIT;

            case 'delete':
            case 'deleteAll':
                return LayerModel::PERMISSION_DELETE;

            case 'copyAll':
                return LayerModel::PERMISSION_CREATE;

            case 'paste':
                $mode = Input::get('mode');

                switch ($mode) {
                    case 'create':
                        return LayerModel::PERMISSION_CREATE;

                    case 'cut':
                        return LayerModel::PERMISSION_EDIT;

                    default:
                        return $permission;
                }

                // Comment just to please phpcs
            default:
                return $permission;
        }
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
            case 'edit':
            case 'delete':
            case 'paste':
            case 'show':
                return (int) Input::get('id');

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                return (int) CURRENT_ID;

            default:
                return null;
        }
    }
}
