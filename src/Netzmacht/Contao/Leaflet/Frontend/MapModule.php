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

class MapModule extends \Module
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_leaflet_map';

    /**
     * @var MapService
     */
    private $mapService;

    /**
     * Construct.
     *
     * @param \ModuleModel $objElement Module model.
     * @param string       $strColumn  Layout column.
     */
    public function __construct($objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);

        $this->mapService = $GLOBALS['container']['leaflet.map.service'];
    }

    public function generate()
    {
        if (TL_MODE === 'BE') {
            $model    = MapModel::findByPK($this->leaflet_map);
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
     * Compile the content element.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function compile()
    {
        try {
            $mapId = 'mod_map_' . ($this->cssID[0] ?: $this->id);
            $map   = $this->mapService->getJavascript($this->leaflet_map, null, $mapId);

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
        } catch(\Exception $e) {
            throw $e;
        }
    }
}
