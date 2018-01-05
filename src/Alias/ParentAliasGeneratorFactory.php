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

namespace Netzmacht\Contao\Leaflet\Alias;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Factory\AliasGeneratorFactory;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;

/**
 * Alias generator validating against the parent id (pid).
 *
 * @package Netzmacht\Contao\Leaflet\Alias
 */
class ParentAliasGeneratorFactory implements AliasGeneratorFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * DefaultAliasGeneratorFactory constructor.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $dataContainerName, string $aliasField, array $fields): AliasGenerator
    {
        $filters = [
            new ExistingAliasFilter(),
            new SlugifyFilter($fields),
            new DefaultAliasFilter($dataContainerName),
            new SuffixFilter(),
        ];

        $validator = new UniqueDatabaseValueValidator($this->connection, $dataContainerName, $aliasField, ['pid']);

        return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField, '_');
    }
}
