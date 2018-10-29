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

namespace Netzmacht\Contao\Leaflet\Frontend;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\InsertTags;

/**
 * Class ValueFilter is a service class which can be used to filter values before passing them to an definition object.
 *
 * @package Netzmacht\Contao\Leaflet\Frontend
 */
class ValueFilter
{
    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * Insert tags.
     *
     * @var InsertTags
     */
    private $replacer;

    /**
     * ValueFilter constructor.
     *
     * @param ContaoFramework $framework Contao framework.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Get the replacer.
     *
     * @return InsertTags
     */
    private function getReplacer(): InsertTags
    {
        if ($this->replacer === null) {
            $this->framework->initialize();
            $this->replacer = $this->framework->createInstance(InsertTags::class);
        }

        return $this->replacer;
    }

    /**
     * Filter a value so it can be passed to the frontend.
     *
     * The idea behind this extra method is that we just have to change one place if anything else than the
     * insert tags has to be replaced.
     *
     * @param string $value The given value.
     *
     * @return string
     */
    public function filter($value)
    {
        $value = $this->getReplacer()->replace($value);
        $value = $this->getReplacer()->replace($value, false);

        return $value;
    }
}
