<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Frontend;

use ContentElement;
use Netzmacht\Contao\DevTools\ServiceContainerTrait;
use Netzmacht\Contao\Leaflet\MapService;

/**
 * The content element for the leaflet map.
 *
 * @property int leaflet_map
 */
class MapElement extends \ContentElement
{
    use ServiceContainerTrait;
    use HybridTrait;

    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'ce_leaflet_map';

    /**
     * {@inheritdoc}
     */
    public function __construct($objElement, $strColumn = 'main')
    {
        $this->construct($objElement, $strColumn);
    }

    /**
     * Get the identifier.
     *
     * @return string
     */
    protected function getIdentifier()
    {
        return 'ce_' . $this->id;
    }
}
