<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Style;

use Netzmacht\LeafletPHP\Definition;

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
    protected static $definitionClass = 'Netzmacht\Contao\Leaflet\Definition\Style\FixedStyle';

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

        $this
            ->addOptions('stroke', 'weight', 'opacity', 'clickable', 'className')
            ->addConditionalOption('color')
            ->addConditionalOption('lineCap')
            ->addConditionalOption('lineJoin')
            ->addConditionalOption('dashArray')
            ->addConditionalOptions('fill', array('fillColor', 'fillOpacity'))
            ->addOption('fill');
    }
}
