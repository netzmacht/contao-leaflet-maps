<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;


use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Definition\Vector\Path;

class AbstractVectorMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\VectorModel';

    protected function initialize()
    {
        parent::initialize();

        $this
            ->addOptions('stroke', 'weight', 'opacity', 'clickable', 'className')
            ->addConditionalOption('color')
            ->addConditionalOption('lineCap')
            ->addConditionalOption('lineJoin')
            ->addConditionalOption('dashArray')
            ->addConditionalOptions('fill', array('fill', 'fillColor', 'fillOpacity'))
        ;
    }

    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $builder,
        LatLngBounds $bounds = null
    ) {
        parent::build($definition, $model, $builder, $bounds);

        if ($definition instanceof Path) {
            if ($model->addPopup) {
                $definition->bindPopup($model->popupContent);
            }
        }
    }
}
