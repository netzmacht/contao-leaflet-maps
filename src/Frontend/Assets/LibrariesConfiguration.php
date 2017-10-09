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

namespace Netzmacht\Contao\Leaflet\Frontend\Assets;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;

/**
 * Class LibrariesConfiguration.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend\Assets
 */
class LibrariesConfiguration implements \IteratorAggregate, \ArrayAccess
{
    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * LibrariesConfiguration constructor.
     *
     * @param ContaoFramework $framework Contao framework.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getIterator()
    {
        $this->framework->initialize();

        return new \ArrayIterator($GLOBALS['LEAFLET_LIBRARIES']);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetExists($offset)
    {
        $this->framework->initialize();

        return isset($GLOBALS['LEAFLET_LIBRARIES'][$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetGet($offset)
    {
        $this->framework->initialize();

        return $GLOBALS['LEAFLET_LIBRARIES'][$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetSet($offset, $value)
    {
        $this->framework->initialize();

        $GLOBALS['LEAFLET_LIBRARIES'][$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetUnset($offset)
    {
        $this->framework->initialize();

        unset($GLOBALS['LEAFLET_LIBRARIES'][$offset]);
    }
}
