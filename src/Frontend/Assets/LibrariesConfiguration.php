<?php

/**
 * Leaflet maps for Contao CMS.
 *
 * @package    contao-leaflet-maps
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumnn <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2014-2023 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Frontend\Assets;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Traversable;

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
    public function getIterator(): Traversable
    {
        $this->framework->initialize();

        return new \ArrayIterator(isset($GLOBALS['LEAFLET_LIBRARIES']) ? $GLOBALS['LEAFLET_LIBRARIES'] : []);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetExists($offset): bool
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
    public function offsetSet($offset, $value): void
    {
        $this->framework->initialize();

        $GLOBALS['LEAFLET_LIBRARIES'][$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function offsetUnset($offset): void
    {
        $this->framework->initialize();

        unset($GLOBALS['LEAFLET_LIBRARIES'][$offset]);
    }
}
