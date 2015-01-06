<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Type;


use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\Icon;
use Netzmacht\LeafletPHP\Definition\Type\LatLngBounds;

class ImageIconMapper extends AbstractIconMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = 'Netzmacht\LeafletPHP\Definition\Type\Icon';

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'image';

    protected function buildConstructArguments(\Model $model, DefinitionMapper $mapper, LatLngBounds $bounds = null)
    {
        $arguments = parent::buildConstructArguments($model, $mapper, $bounds);

        if ($model->iconImage) {
            $file = \FilesModel::findByUuid($model->iconImage);

            if ($file) {
                $arguments[] = $file->path;
            }
        }

        return $arguments;
    }


    protected function doBuild(
        Definition $definition,
        \Model $model,
        DefinitionMapper $mapper,
        LatLngBounds $bounds = null
    ) {
        if ($definition instanceof Icon) {
            $this->addIcon($definition, $model);
            $this->addShadow($definition, $model);
        }
    }

    /**
     * @param Icon      $definition
     * @param IconModel $model
     */
    private function addIcon(Icon $definition, IconModel $model)
    {
        if ($model->iconImage) {
            $file = \FilesModel::findByUuid($model->iconImage);

            if ($file) {
                $definition->setIconUrl($file->path);

                $file = new \File($file->path);
                $definition->setIconSize(array($file->width, $file->height));

                if (!$model->iconAnchor) {
                    $definition->setIconAnchor(array($file->width / 2, $file->height));
                }

                if (!$model->popupAnchor) {
                    $definition->setPopupAnchor(array(0, 10 - $file->height));
                }
            }
        }

        if ($model->iconAnchor) {
            $definition->setIconAnchor(array_map('intval', explode(',', $model->iconAnchor)));
        }

        if ($model->iconRetinaImage) {
            $file = \FilesModel::findByUuid($model->iconRetinaImage);

            if ($file) {
                $definition->setIconRetinaUrl($file->path);
            }
        }
    }

    private function addShadow(Icon $definition, $model)
    {
        if ($model->shadowImage) {
            $file = \FilesModel::findByUuid($model->shadowImage);

            if ($file) {
                $definition->setShadowUrl($file->path);

                $file = new \File($file->path);
                $definition->setShadowSize(array($file->width, $file->height));

                if (!$model->shadowAnchor) {
                    $definition->setShadowAnchor(array($file->width / 2, $file->height));
                }
            }
        }

        if ($model->shadowAnchor) {
            $definition->setShadowAnchor(array_map('intval', explode(',', $model->shadowAnchor)));
        }

        if ($model->shadowRetinaImage) {
            $file = \FilesModel::findByUuid($model->shadowRetinaImage);

            if ($file) {
                $definition->setShadowRetinaUrl($file->path);
            }
        }
    }
}
