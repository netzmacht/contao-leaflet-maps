<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet;

/**
 * @property int leaflet_map
 */
class LeafletMapElement extends \ContentElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_leaflet_map';

    /**
     * @var MapService
     */
    private $mapService;

    /**
     * Construct.
     *
     * @param \ContentModel $objElement Content element model.
     * @param string        $strColumn  Layout column.
     */
    public function __construct($objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);

        $this->mapService = $GLOBALS['container']['leaflet.map.service'];
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
            $mapId = 'map_' . ($this->cssID[0] ?: $this->id);
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
