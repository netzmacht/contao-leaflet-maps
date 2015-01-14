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

use Netzmacht\Contao\Leaflet\MapService;
use Netzmacht\Contao\Leaflet\Model\MapModel;

/**
 * Class HybridTrait provides method required by the frontend module and content element the same time.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
trait HybridTrait
{
    /**
     * The map service.
     *
     * @var MapService
     */
    private $mapService;

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
     * Construct.
     *
     * @param \ContentModel $objElement Content element model.
     * @param string        $strColumn  Layout column.
     *
     * @return void
     */
    protected function construct($objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);

        $this->mapService = static::getService('leaflet.map.service');
        $this->input      = static::getService('input');
        $this->config     = static::getService('config');
    }

    /**
     * Do the frontend integration generation.
     *
     * @return string
     */
    public function generate()
    {
        $this->handleAjaxRequest();

        if (TL_MODE === 'BE') {
            $model = MapModel::findByPK($this->leaflet_map);

            $template = new \BackendTemplate('be_wildcard');

            if ($model) {
                $href = 'contao/main.php?do=leaflet&amp;table=tl_leaflet_map&amp;act=edit&amp;id=' . $model->id;

                $template->wildcard = '### LEAFLET MAP ' . $model->title . ' ###';
                $template->title    = $this->headline;
                $template->id       = $model->id;
                $template->link     = $model->title;
                $template->href     = $href;
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
     * @throws \Exception If something went wrong.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function compile()
    {
        try {
            RequestUrl::setFor($this->getIdentifier());
            $mapId = 'map_' . ($this->cssID[0] ?: ('ce_' . $this->id));
            $map   = $this->mapService->generate($this->leaflet_map, null, $mapId);
            RequestUrl::setFor(null);

            $GLOBALS['TL_BODY'][] = '<script>' . $map .'</script>';

            $this->Template->mapId = $mapId;

            $style  = '';
            $height = deserialize($this->leaflet_height, true);
            $width  = deserialize($this->leaflet_width, true);

            if (!empty($width['value'])) {
                $style .= 'width:' . $width['value'] . $width['unit'] . ';';
            }

            if (!empty($height['value'])) {
                $style .= 'height:' . $height['value'] . $height['unit'] . ';';
            }

            $this->Template->mapStyle = $style;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle ajax request if leaflet parameter is given.
     *
     * @throws \Exception
     */
    private function handleAjaxRequest()
    {
        $input = $this->input->get('leaflet');

        // Handle ajax request.
        if ($input) {
            $data = (array) explode(',', base64_decode($input));
            $data = array_combine(array('for', 'type', 'id', 'format'), $data);
            $data = array_filter($data);

            if (empty($data['for']) || $data['for'] != $this->getIdentifier()) {
                return;
            }

            $controller = new DataController($this->mapService, $data);
            $controller->execute();

            exit;
        }
    }
}
