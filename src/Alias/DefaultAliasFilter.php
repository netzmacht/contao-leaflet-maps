<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Leaflet\Alias;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractFilter;

/**
 * Class DefaultAliasFilter creates an prefix of the alias.
 *
 * @package Netzmacht\Contao\Leaflet\Alias
 */
class DefaultAliasFilter extends AbstractFilter
{
    /**
     * Alias prefix.
     *
     * @var string
     */
    private $prefix;

    /**
     * DefaultAliasFilter constructor.
     *
     * @param string $dataContainerName Data container name.
     * @param int    $combine           Combine strategy.
     */
    public function __construct($dataContainerName, $combine = self::COMBINE_REPLACE)
    {
        parent::__construct(true, $combine);

        $this->prefix = str_replace('tl_leaflet_', '', $dataContainerName);
    }

    /**
     * {@inheritdoc}
     */
    public function repeatUntilValid(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, string $separator): string
    {
        if (!$value) {
            return $this->prefix . $separator . $model->id;
        }

        return $value;
    }
}
