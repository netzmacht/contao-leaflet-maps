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

namespace Netzmacht\Contao\Leaflet\Frontend;

use Contao\Database\Result;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Component\Hybrid\AbstractHybrid;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class HybridTrait provides method required by the frontend module and content element the same time.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
abstract class AbstractMapHybrid extends AbstractHybrid
{
    /**
     * The map provider.
     *
     * @var MapProvider
     */
    private $mapProvider;

    /**
     * The user input.
     *
     * @var Input
     */
    private $input;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * HybridTrait constructor.
     *
     * @param Result|Model|Collection $model             Component model.
     * @param TemplateEngine          $templateEngine    Template engine.
     * @param Translator              $translator        Translator.
     * @param MapProvider             $mapProvider       Map provider.
     * @param RepositoryManager       $repositoryManager Repository manager.
     * @param Input                   $input             Request Input.
     * @param string                  $column            Column in which the element appears.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        Translator $translator,
        MapProvider $mapProvider,
        RepositoryManager $repositoryManager,
        $input,
        $column = null
    ) {
        parent::__construct($model, $templateEngine, $translator, $column);

        $this->mapProvider       = $mapProvider;
        $this->input             = $input;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * Do the frontend integration generation.
     *
     * @return string
     */
    public function generate(): string
    {
        $this->mapProvider->handleAjaxRequest($this->getIdentifier());

        if (TL_MODE === 'BE') {
            $repository = $this->repositoryManager->getRepository(MapModel::class);
            $model      = $repository->find((int) $this->get('leaflet_map'));
            $parameters = [
                'title' => $this->get('headline'),
            ];

            if ($model) {
                $href = 'contao/main.php?do=leaflet&amp;table=tl_leaflet_map&amp;act=edit&amp;id=' . $model->id;

                $parameters['wildcard'] = '### LEAFLET MAP ' . $model->title . ' ###';
                $parameters['id']       = $model->id;
                $parameters['link']     = $model->title;
                $parameters['href']     = $href;
            }

            return $this->render('toolkit:be:be_wildcard.html5', $parameters);
        }

        return parent::generate();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception If map could not be created.
     */
    protected function prepareTemplateData(array $data): array
    {
        $data = parent::prepareTemplateData($data);

        try {
            $template = $this->get('leaflet_template') ?: 'leaflet_map_js';
            $mapId    = $this->getIdentifier();
            $map      = $this->mapProvider->generate($this->get('leaflet_map'), null, $mapId, $template);

            $data['javascript'] = $map;
            $data['mapId']      = $mapId;

            $style  = '';
            $height = StringUtil::deserialize($this->get('leaflet_height'), true);
            $width  = StringUtil::deserialize($this->get('leaflet_width'), true);

            if (!empty($width['value'])) {
                $style .= 'width:' . $width['value'] . $width['unit'] . ';';
            }

            if (!empty($height['value'])) {
                $style .= 'height:' . $height['value'] . $height['unit'] . ';';
            }

            $data['mapStyle'] = $style;
        } catch (\Exception $e) {
            throw $e;
        }

        return $data;
    }

    /**
     * Get the component identifier which is used as unique name.
     *
     * @return string
     */
    abstract protected function getIdentifier(): string;
}
