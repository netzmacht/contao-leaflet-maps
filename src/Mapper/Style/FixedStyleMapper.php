<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Style;

use Netzmacht\Contao\Leaflet\Definition\Style\FixedStyle;

/**
 * Class FixedStyleMapper maps the fixed style to the corresponding definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Style
 */
class FixedStyleMapper extends AbstractStyleMapper
{
    /**
     * Definition class.
     *
     * @var string
     */
    protected static $definitionClass = FixedStyle::class;

    /**
     * Style type.
     *
     * @var string
     */
    protected static $type = 'fixed';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder
            ->addOptions('stroke', 'weight', 'opacity', 'clickable', 'className')
            ->addConditionalOption('color')
            ->addConditionalOption('lineCap')
            ->addConditionalOption('lineJoin')
            ->addConditionalOption('dashArray')
            ->addConditionalOptions('fill', array('fillColor', 'fillOpacity'))
            ->addOption('fill');
    }
}
