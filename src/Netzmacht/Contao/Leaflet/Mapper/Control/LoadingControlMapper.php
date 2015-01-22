<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Control;

use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\ControlModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Control\Zoom;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;
use Netzmacht\LeafletPHP\Plugins\Loading\LoadingControl;
use Netzmacht\LeafletPHP\Plugins\Loading\SpinJsLoadingControl;

/**
 * Class LoadingControlMapper maps the control model to the loading control definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Control
 */
class LoadingControlMapper extends AbstractControlMapper
{
    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'loading';

    /**
     * {@inheritdoc}
     */
    protected function getClassName(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        if ($model->spinjs) {
            return 'Netzmacht\LeafletPHP\Plugins\Loading\SpinJsLoadingControl';
        }

        return 'Netzmacht\LeafletPHP\Plugins\Loading\LoadingControl';
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->optionsBuilder->addOption('separate');
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null,
        Definition $parent = null
    ) {
        parent::build($definition, $model, $mapper, $bounds);

        if ($definition instanceof SpinJsLoadingControl && $model->spin) {
            $config = json_decode($model->spin, true);

            if (is_array($config)) {
                $definition->setSpin($config);
            }
        }

        if ($definition instanceof LoadingControl && !$definition->isSeparate() && $model->zoomControl) {
            // Only assign if zoom control is activated and part of the map.
            $control = ControlModel::findOneBy(
                array('active=1', 'type=?', 'pid=?', 'id=?'),
                array('zoom', $model->pid, $model->zoomControl)
            );

            if ($control) {
                $control = $mapper->handle($control);

                if ($control instanceof Zoom) {
                    // By default the loading control overrides the position of the zoom control. Deactivate it by
                    // overriding the position.
                    $definition->setPosition($control->getPosition());
                    $definition->setZoomControl($control);
                }
            }
        }
    }
}
