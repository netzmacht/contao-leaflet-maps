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

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener;

use Netzmacht\Contao\Leaflet\ContaoAssets;
use Netzmacht\Contao\Leaflet\Frontend\Assets\LibrariesConfiguration;
use Netzmacht\Contao\Leaflet\Mapper\DefinitionMapper;
use Netzmacht\Contao\Leaflet\Model\IconModel;
use Netzmacht\LeafletPHP\Assets;
use Netzmacht\LeafletPHP\Definition\Type\Icon;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;

/**
 * Class LoadAssetsListener.
 *
 * @package Netzmacht\Contao\Leaflet\Listener
 */
class LoadAssetsListener
{
    /**
     * Assets.
     *
     * @var Assets
     */
    private $assets;

    /**
     * Definition mapper.
     *
     * @var DefinitionMapper
     */
    private $definitionMapper;

    /**
     * Libraries.
     *
     * @var LibrariesConfiguration
     */
    private $libraries;

    /**
     * LoadAssetsListener constructor.
     *
     * @param Assets                 $assets           Assets.
     * @param DefinitionMapper       $definitionMapper Definition mapper.
     * @param LibrariesConfiguration $libraries        Libraries.
     */
    public function __construct(Assets $assets, DefinitionMapper $definitionMapper, LibrariesConfiguration $libraries)
    {
        $this->assets           = $assets;
        $this->definitionMapper = $definitionMapper;
        $this->libraries        = $libraries;
    }

    /**
     * Handle the get javascript event.
     *
     * @return void
     */
    public function onGetJavascriptEvent()
    {
        $this->assets->addJavascript(
            'bundles/netzmachtcontaoleaflet/js/contao-leaflet.js',
            ContaoAssets::TYPE_FILE
        );

        $collection = IconModel::findBy('active', true);

        if ($collection) {
            $buffer = '';
            $icons  = [];

            foreach ($collection as $model) {
                /** @var ImageIcon $icon */
                $icon    = $this->definitionMapper->handle($model);
                $icons[] = [
                    'id'      => $icon->getId(),
                    'type'    => lcfirst($icon->getType()),
                    'options' => $icon->getOptions(),
                ];

                $this->loadIconsLibraries($icon);
            }

            if ($icons) {
                $buffer = sprintf('L.contao.loadIcons(%s);', json_encode($icons));
            }

            // @codingStandardsIgnoreStart
            // TODO: Cache it.
            // codingStandardsIgnoreEnd

            $file = new \File('assets/leaflet/js/icons.js');
            $file->write($buffer);
            $file->close();

            $this->assets->addJavascript('assets/leaflet/js/icons.js', ContaoAssets::TYPE_FILE);
        }
    }

    /**
     * Load all libraries for an icon.
     *
     * @param Icon $icon Icon definition.
     *
     * @return void
     */
    protected function loadIconsLibraries($icon)
    {
        foreach ($icon::getRequiredLibraries() as $library) {
            if (!isset($this->libraries[$library])) {
                continue;
            }

            $assets = $this->libraries[$library];

            if (!empty($assets['css'])) {
                list ($source, $type) = (array)$assets['css'];
                $this->assets->addStylesheet($source, $type ?: Assets::TYPE_FILE);
            }

            if (!empty($assets['javascript'])) {
                list ($source, $type) = (array)$assets['javascript'];
                $this->assets->addJavascript($source, $type ?: Assets::TYPE_FILE);
            }
        }
    }
}
