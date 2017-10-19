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

namespace Netzmacht\Contao\Leaflet\Mapper\Vector;

use Contao\Model;
use Netzmacht\Contao\Leaflet\Definition\Style;
use Netzmacht\Contao\Leaflet\Frontend\ValueFilter;
use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Mapper\Request;
use Netzmacht\Contao\Leaflet\Model\PopupModel;
use Netzmacht\Contao\Leaflet\Model\StyleModel;
use Netzmacht\Contao\Leaflet\Model\VectorModel;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\LeafletPHP\Definition;
use Netzmacht\LeafletPHP\Definition\HasPopup;
use Netzmacht\LeafletPHP\Definition\UI\Popup;
use Netzmacht\LeafletPHP\Definition\Vector\Path;

/**
 * Class AbstractVectorMapper is the base class for the vector model definition mapping.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Vector
 */
class AbstractVectorMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = VectorModel::class;

    /**
     * Frontend filter.
     *
     * @var ValueFilter
     */
    protected $valueFilter;

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
     * @param ValueFilter       $valueFilter       Frontend filter.
     */
    public function __construct(RepositoryManager $repositoryManager, ValueFilter $valueFilter)
    {
        $this->repositoryManager = $repositoryManager;
        $this->valueFilter       = $valueFilter;

        parent::__construct();
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
        parent::build($definition, $model, $mapper, $request);

        if ($definition instanceof Path && $model->style) {
            $repository = $this->repositoryManager->getRepository(StyleModel::class);
            $styleModel = $repository->findActiveByPK($model->style);

            if ($styleModel) {
                $style = $mapper->handle($styleModel);

                if ($style instanceof Style) {
                    $style->apply($definition);
                }
            }
        }

        $this->buildPopup($definition, $model, $mapper, $request);
    }

    /**
     * Build the popup.
     *
     * @param Definition       $definition The definition.
     * @param Model            $model      The model.
     * @param DefinitionMapper $mapper     The definition mapper.
     * @param Request          $request    Optional building request.
     *
     * @return void
     */
    protected function buildPopup(
        Definition $definition,
        Model $model,
        DefinitionMapper $mapper,
        Request $request = null
    ) {
        if ($definition instanceof HasPopup && $model->addPopup) {
            $popup   = null;
            $content = $this->valueFilter->filter($model->popupContent);

            if ($model->popup) {
                $repository = $this->repositoryManager->getRepository(PopupModel::class);
                $popupModel = $repository->findActiveByPK($model->popup);

                if ($popupModel) {
                    $popup = $mapper->handle($popupModel, $request, null, $definition);
                }
            }

            if ($popup instanceof Popup) {
                $definition->bindPopup($content, $popup->getOptions());
            } else {
                $definition->bindPopup($content);
            }
        }
    }
}
