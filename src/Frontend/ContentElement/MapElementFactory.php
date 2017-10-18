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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Frontend\ContentElement;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Leaflet\MapProvider;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class MapElementFactory
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\ContentElement
 */
class MapElementFactory implements ComponentFactory
{
    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private $templating;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Map provider.
     *
     * @var MapProvider
     */
    private $mapProvider;

    /**
     * Input adapter.
     *
     * @var Input|Adapter
     */
    private $input;

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * MapElementFactory constructor.
     *
     * @param TemplateEngine    $engine            Template engine.
     * @param Translator        $translator        Translator.
     * @param MapProvider       $mapProvider       Map provider.
     * @param RepositoryManager $repositoryManager Repository manager.
     * @param Input|Adapter     $input             Input adapter.
     */
    public function __construct(
        TemplateEngine $engine,
        Translator $translator,
        MapProvider $mapProvider,
        RepositoryManager $repositoryManager,
        $input
    ) {
        $this->templating        = $engine;
        $this->translator        = $translator;
        $this->mapProvider       = $mapProvider;
        $this->input             = $input;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($model): bool
    {
        return $model->type === 'leaflet';
    }

    /**
     * {@inheritDoc}
     */
    public function create($model, string $column): Component
    {
        return new MapElement(
            $model,
            $this->templating,
            $this->translator,
            $this->mapProvider,
            $this->repositoryManager,
            $this->input,
            $column
        );
    }
}
