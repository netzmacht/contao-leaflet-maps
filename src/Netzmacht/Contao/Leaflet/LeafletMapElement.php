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
            $map   = $this->mapService->getJavascript($this->leaflet_map, $mapId);

            $GLOBALS['TL_BODY'][] = '<script>' . $map .'</script>';

            $this->Template->mapId = $mapId;
//            $this->Template->map   = $map;
        } catch(\Exception $e) {
            throw $e;
        }
    }
}
