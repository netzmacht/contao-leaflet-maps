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
use Contao\Image;
use Contao\RequestToken;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Leaflet\Backend\Renderer\Label\Layer\LayerLabelRenderer;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Toolkit\Dca\Listener\AbstractListener;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder;
use Netzmacht\Contao\Leaflet\Model\LayerModel;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
     * Construct.
     *
     * @param Manager            $manager       Data container manager.
     * @param Connection         $connection    Database connection.
     * @param Translator         $translator    Translator.
     * @param LayerLabelRenderer $labelRenderer Layer label renderer.
     * @param array              $layers        Leaflet layer configuration.
     * @param array              $tileProviders Tile providers.
     * @param array              $amenities     OSM amenities.
     */
    public function __construct(
        Manager $manager,
        Connection $connection,
        Translator $translator,
        LayerLabelRenderer $labelRenderer,
        array $layers,
        array $tileProviders,
        array $amenities
    ) {
        parent::__construct($manager);

        Controller::loadLanguageFile('leaflet_layer');

        $this->connection    = $connection;
        $this->layers        = $layers;
        $this->tileProviders = $tileProviders;
        $this->translator    = $translator;
        $this->amenities     = $amenities;
        $this->labelRenderer = $labelRenderer;
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

        return array();
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

        if (!$row['active']) {
            $src = preg_replace('/(\.[^\.]+)$/', '_1$1', $src);
        }

        $alt   = $this->getFormatter()->formatValue('type', $row['type']);
        $icon  = Image::getHtml($src, $alt, sprintf('title="%s"', StringUtil::specialchars(strip_tags($alt))));
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

        $collection = LayerModel::findMultipleByTypes($types);
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
        $pasteAfterUrl = \Controller::addToUrl(
            'act='.$children['mode'].'&amp;mode=1&amp;pid='.$row['id']
            .(!is_array($children['id']) ? '&amp;id='.$children['id'] : '')
            . '&amp;rt=' . RequestToken::get()
        );

        $buffer = sprintf(
            '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
            $pasteAfterUrl,
            specialchars($this->translator->trans('pasteafter.1', [$row['id']], 'contao_' . $table)),
            \Image::getHtml(
                'pasteafter.gif',
                $this->translator->trans('pasteafter.1', [$row['id']], 'contao_' . $table)
            )
        );

        if (!empty($this->layers[$row['type']]['children'])) {
            $pasteIntoUrl = \Controller::addToUrl(
                sprintf(
                    'act=%s&amp;mode=2&amp;pid=%s%s',
                    $children['mode'],
                    $row['id'],
                    !is_array($children['id']) ? '&amp;id='.$children['id'] : ''
                ) . '&amp;rt=' . RequestToken::get()
            );

            $buffer .= sprintf(
                '<a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a> ',
                $pasteIntoUrl,
                specialchars($this->translator->trans('pasteinto.1', [$row['id']], 'contao_' . $table)),
                \Image::getHtml(
                    'pasteinto.gif',
                    $this->translator->trans('pasteinto.1', [$row['id']], 'contao_' . $table)
                )
            );
        } elseif ($row['id'] > 0) {
            $buffer .= \Image::getHtml('pasteinto_.gif');
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
            $statement->execute();

            $undo = $statement->fetch();

            $result = $this->connection
                ->prepare('SELECT * FROM tl_leaflet_map_layer WHERE lid=?')
                ->execute($dataContainer->id);

            $undo['data'] = deserialize($undo['data'], true);

            while ($result->next()) {
                $undo['data']['tl_leaflet_map_layer'][] = $result->row();
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
        $options = array();

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
        $collection = LayerModel::findBy('id !', $dataContainer->id);

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
        $collection = IconModel::findAll(array('order' => 'title'));
        $builder    = OptionsBuilder::fromCollection(
            $collection,
            function ($model) {
                return sprintf('%s [%s]', $model['title'], $model['type']);
            }
        );

        return $builder->getOptions();
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
            \Backend::addToUrl($href . '&amp;id=' . $row['id']),
            $title,
            \Image::getHtml($icon, $label, $attributes)
        );
    }
}
