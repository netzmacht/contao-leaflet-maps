<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Leaflet\Backend\Action;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Credits backend module.
 *
 * @package Netzmacht\Contao\Leaflet\Backend
 */
final class AboutAction
{
    /**
     * Twig environment.
     *
     * @var Environment
     */
    private $twig;

    /**
     * Project directory.
     *
     * @var string
     */
    private $projectDir;

    /**
     * AboutAction constructor.
     *
     * @param Environment $twig       Twig environment.
     * @param string      $projectDir Project directory.
     */
    public function __construct(Environment $twig, string $projectDir)
    {
        $this->twig       = $twig;
        $this->projectDir = $projectDir;
    }

    /**
     * Generate the backend view.
     *
     * @return string
     */
    public function __invoke(): Response
    {
        $data = [
            'headline'  => 'Leaftlet maps integration for Contao CMS',
            'libraries' => $this->getLibraries(),
        ];

        [$data['version'], $data['dependencies']] = $this->extractFromComposer();

        return new Response(
            $this->twig->render('@NetzmachtContaoLeaflet/backend/about.html.twig', $data)
        );
    }

    /**
     * Get list of all libraries.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getLibraries(): array
    {
        return array_map(
            function ($library) {
                $library = array_merge(
                    [
                        'homepage' => null,
                        'version'  => null,
                    ],
                    $library
                );

                if ($library['homepage']) {
                    $library['homepage'] = sprintf(
                        '<a href="%s" target="_blank">%s</a>',
                        $library['homepage'],
                        preg_replace('#^(https?://(www)?)#i', '', $library['homepage'])
                    );
                }

                return $library;
            },
            array_filter(
                $GLOBALS['LEAFLET_LIBRARIES'],
                function ($library) {
                    return isset($library['name'], $library['license']);
                }
            )
        );
    }

    /**
     * Extract version and dependencies from composer.
     *
     * @return array
     */
    private function extractFromComposer(): array
    {
        $extFile  = $this->projectDir . '/vendor/netzmacht/contao-leaflet-maps/composer.json';
        $lockFile = $this->projectDir . '/composer.lock';

        if (!file_exists($extFile) || !file_exists($lockFile)) {
            return [];
        }

        $extension = json_decode(file_get_contents($extFile), true);
        $installed = json_decode(file_get_contents($lockFile), true);
        $deps      = [];
        $version   = null;

        foreach ($installed['packages'] as $package) {
            if ($package['name'] === 'netzmacht/contao-leaflet-maps') {
                $version = $package['version'];
            } elseif (isset($extension['require'][$package['name']])) {
                $deps[] = [
                    'name'     => $package['name'],
                    'version'  => $package['version'],
                    'license'  => !empty($package['license']) ? implode(', ', $package['license']) : '',
                    'homepage' => sprintf(
                        '<a href="https://packagist.org/packages/%s" target="_blank">Visit packagist</a>',
                        $package['name']
                    ),
                ];
            }
        }

        return [$version, $deps];
    }
}
