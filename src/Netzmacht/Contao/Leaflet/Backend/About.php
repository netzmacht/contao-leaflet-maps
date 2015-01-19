<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Backend;

/**
 * Credits backend module.
 *
 * @package Netzmacht\Contao\Leaflet\Backend
 */
class About
{
    /**
     * Generate the backend view.
     *
     * @return string
     */
    public function generate()
    {
        $template = new \BackendTemplate('be_leaflet_credits');

        $template->headline  = 'Leaftlet maps integration for Contao CMS';
        $template->libraries = $this->getLibraries();

        list($template->version, $template->dependencies) = $this->extractFromComposer();

        return $template->parse();
    }

    /**
     * Get list of all libraries.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getLibraries()
    {
        return array_map(
            function ($library) {
                $library = array_merge(
                    array(
                        'homepage' => null,
                        'version'  => null,
                    ),
                    $library
                );

                if ($library['homepage']) {
                    $library['homepage'] = sprintf(
                        '<a href="%s" %s>%s</a>',
                        $library['homepage'],
                        LINK_NEW_WINDOW,
                        preg_replace('#^(https?://(www)?)#i', '', $library['homepage'])
                    );
                }

                return $library;
            },
            array_filter(
                $GLOBALS['LEAFLET_LIBRARIES'],
                function ($library) {
                    return isset($library['name']) && isset($library['license']);
                }
            )
        );
    }

    /**
     * Extract version and dependencies from composer.
     *
     * @return array
     */
    private function extractFromComposer()
    {
        $local    = TL_ROOT . '/composer/vendor/netzmacht/contao-leaflet-maps/composer.json';
        $composer = TL_ROOT . '/composer/composer.lock';

        if (!file_exists($local)) {
            return array();
        }

        $local     = json_decode(file_get_contents($local), true);
        $installed = json_decode(file_get_contents($composer), true);
        $deps      = array();
        $version   = null;

        foreach ($installed['packages'] as $package) {
            if ($package['name'] === 'netzmacht/contao-leaflet-maps') {
                $version = $package['version'];
            } elseif (isset($local['require'][$package['name']])) {
                $deps[] = array(
                    'name'     => $package['name'],
                    'version'  => $package['version'],
                    'license'  => !empty($package['license']) ? implode(', ', $package['license']) : '',
                    'homepage' => sprintf(
                        '<a href="https://packagist.org/packages/%s" target="_blank">Visit packagist</a>',
                        $package['name']
                    )
                );
            }
        }

        return array($version, $deps);
    }
}
