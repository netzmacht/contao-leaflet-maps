<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2018 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Listener\Backend;

use Contao\CoreBundle\Event\MenuEvent;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface as Router;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class BackendMenuListener adds backend entries to the navigation.
 */
final class UserNavigationListener
{
    /**
     * Request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Router.
     *
     * @var Router
     */
    private $router;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Assets manager.
     *
     * @var AssetsManager
     */
    private $assets;

    /**
     * BackendUserNavigationListener constructor.
     *
     * @param RequestStack  $requestStack Request stack.
     * @param Router        $router       Router.
     * @param Translator    $translator   Translator.
     * @param AssetsManager $assets       Assets manager.
     */
    public function __construct(
        RequestStack $requestStack,
        Router $router,
        Translator $translator,
        AssetsManager $assets
    ) {
        $this->requestStack = $requestStack;
        $this->router       = $router;
        $this->translator   = $translator;
        $this->assets       = $assets;
    }

    /**
     * Handle the event.
     *
     * @param array $modules Backend navigation modules.
     *
     * @return array
     */
    public function __invoke(array $modules): array
    {
        if (!isset($modules['leaflet'])) {
            return $modules;
        }

        $request  = $this->requestStack->getCurrentRequest();
        $isActive = $request && $request->attributes->get('_backend_module') === 'leaflet_about';

        $modules['leaflet']['modules']['leaflet_about'] = [
            'label'    => $this->translator->trans('MOD.leaflet_about.0', [], 'contao_modules'),
            'title'    => $this->translator->trans('MOD.leaflet_about.1', [], 'contao_modules'),
            'href'     => $this->router->generate('leaflet_backend_about'),
            'icon'     => 'bundles/netzmachtcontaoleaflet/img/about.png',
            'class'    => 'navigation leaflet_about',
            'isActive' => $isActive,
        ];

        if ($isActive) {
            $this->assets->addStylesheet('bundles/netzmachtcontaoleaflet/css/about.css');
        }

        return $modules;
    }
}
