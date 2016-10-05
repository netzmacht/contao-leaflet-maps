<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Database\Result;
use Model\Collection;
use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Leaflet\Model\MapModel;
use Netzmacht\Contao\Toolkit\Component\Hybrid\AbstractHybrid;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

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
     * @var \Input
     */
    private $input;

    /**
     * The Contao config.
     *
     * @var \Config
     */
    private $config;

    /**
     * HybridTrait constructor.
     *
     * @param Result|\Model|Collection $model           Component model.
     * @param TemplateFactory          $templateFactory Template factory.
     * @param Translator               $translator      Translator.
     * @param MapProvider              $mapProvider     Map provider.
     * @param \Input                   $input           Request Input.
     * @param \Config                  $config          Config.
     * @param string                   $column          Column in which the element appears.
     */
    public function __construct(
        $model,
        TemplateFactory $templateFactory,
        Translator $translator,
        MapProvider $mapProvider,
        \Input $input,
        \Config $config,
        $column = null
    ) {
        parent::__construct($model, $templateFactory, $translator, $column);

        $this->mapProvider = $mapProvider;
        $this->input       = $input;
        $this->config      = $config;
    }

    /**
     * Do the frontend integration generation.
     *
     * @return string
     */
    public function generate()
    {
        $this->mapProvider->handleAjaxRequest($this->getIdentifier());

        if (TL_MODE === 'BE') {
            $model = MapModel::findByPk($this->get('leaflet_map'));

            $template = $this->getTemplateFactory()->createBackendTemplate('be_wildcard');

            if ($model) {
                $href = 'contao/main.php?do=leaflet&amp;table=tl_leaflet_map&amp;act=edit&amp;id=' . $model->id;

                $template->set('wildcard', '### LEAFLET MAP ' . $model->title . ' ###');
                $template->set('title', $this->get('headline'));
                $template->set('id', $model->id);
                $template->set('link', $model->title);
                $template->set('href', $href);
            }

            return $template->parse();
        }

        return parent::generate();
    }

    /**
     * Do the frontend integration compiling.
     *
     * @return void
     *
     * @throws \Exception If the map could not be created.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function compile()
    {
        try {
            $template = $this->get('leaflet_template') ?: 'leaflet_map_js';
            $mapId    = $this->getIdentifier();
            $map      = $this->mapProvider->generate($this->get('leaflet_map'), null, $mapId, $template);

            $GLOBALS['TL_BODY'][] = '<script>' . $map .'</script>';

            $this->template->set('mapId', $mapId);

            $style  = '';
            $height = deserialize($this->get('leaflet_height'), true);
            $width  = deserialize($this->get('leaflet_width'), true);

            if (!empty($width['value'])) {
                $style .= 'width:' . $width['value'] . $width['unit'] . ';';
            }

            if (!empty($height['value'])) {
                $style .= 'height:' . $height['value'] . $height['unit'] . ';';
            }

            $this->template->set('mapStyle', $style);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the component identifier which is used as unique name.
     *
     * @return string
     */
    abstract protected function getIdentifier();
}
