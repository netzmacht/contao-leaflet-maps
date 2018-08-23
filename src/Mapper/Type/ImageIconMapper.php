<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Type;

use Contao\File;
use Contao\FilesModel;
use Contao\Model;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;

/**
 * Class ImageIconMapper maps the icon model to the image icon definition.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Type
 */
class ImageIconMapper extends AbstractIconMapper
{
    /**
     * Class of the definition being created.
     *
     * @var string
     */
    protected static $definitionClass = ImageIcon::class;

    /**
     * Layer type.
     *
     * @var string
     */
    protected static $type = 'image';

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * Construct.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function buildConstructArguments(
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        $elementId = null
    ) {
        $arguments = parent::buildConstructArguments($model, $mapper, $request, $elementId);

        if ($model->iconImage) {
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            $file       = $repository->findByUuid($model->iconImage);

            if ($file) {
                $arguments[] = $file->path;
            }
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null,
        Definition $parent = null
    ) {
        if ($definition instanceof ImageIcon) {
            $this->addIcon($definition, $model);
            $this->addShadow($definition, $model);
        }
    }

    /**
     * Add icon image.
     *
     * @param ImageIcon $definition The icon definition.
     * @param IconModel $model      The model.
     *
     * @return void
     */
    private function addIcon(ImageIcon $definition, IconModel $model)
    {
        $repository = $this->repositoryManager->getRepository(FilesModel::class);

        if ($model->iconImage) {
            $file = $repository->findByUuid($model->iconImage);

            if ($file) {
                $definition->setIconUrl($file->path);

                $file = new File($file->path);
                $definition->setIconSize([$file->width, $file->height]);

                if (!$model->iconAnchor) {
                    $definition->setIconAnchor([($file->width / 2), $file->height]);
                }

                if (!$model->popupAnchor) {
                    $definition->setPopupAnchor([0, (8 - $file->height)]);
                }
            }
        }

        if ($model->iconAnchor) {
            $definition->setIconAnchor(array_map('intval', explode(',', $model->iconAnchor)));
        }

        if ($model->iconRetinaImage) {
            $file = $repository->findByUuid($model->iconRetinaImage);

            if ($file) {
                $definition->setIconRetinaUrl($file->path);
            }
        }
    }

    /**
     * Add shadow if defined.
     *
     * @param ImageIcon $definition The icon definition.
     * @param IconModel $model      The model.
     *
     * @return void
     */
    private function addShadow(ImageIcon $definition, $model)
    {
        $repository = $this->repositoryManager->getRepository(FilesModel::class);

        if ($model->shadowImage) {
            $file = $repository->findByUuid($model->shadowImage);

            if ($file) {
                $definition->setShadowUrl($file->path);

                $file = new File($file->path);
                $definition->setShadowSize([$file->width, $file->height]);

                if (!$model->shadowAnchor) {
                    $definition->setShadowAnchor([($file->width / 2), $file->height]);
                }
            }
        }

        if ($model->shadowAnchor) {
            $definition->setShadowAnchor(array_map('intval', explode(',', $model->shadowAnchor)));
        }

        if ($model->shadowRetinaImage) {
            $file = $repository->findByUuid($model->shadowRetinaImage);

            if ($file) {
                $definition->setShadowRetinaUrl($file->path);
            }
        }
    }
}
