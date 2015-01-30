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

use Netzmacht\Contao\Toolkit\ServiceContainerTrait;
use Netzmacht\Contao\Leaflet\MapService;

/**
 * The frontend module for the Leaflet map.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class MapModule extends \Module
{
    use ServiceContainerTrait;
    use HybridTrait;

    /**
     * Template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_leaflet_map';

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
        return 'mod_' . $this->id;
    }
}
