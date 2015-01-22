<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Mapper\Type;

use Netzmacht\Contao\Leaflet\Mapper\AbstractTypeMapper;

/**
 * Class AbstractIconMapper is the base mapper for the icon model.
 *
 * @package Netzmacht\Contao\Leaflet\Mapper\Type
 */
class AbstractIconMapper extends AbstractTypeMapper
{
    /**
     * Class of the model being build.
     *
     * @var string
     */
    protected static $modelClass = 'Netzmacht\Contao\Leaflet\Model\IconModel';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->optionsBuilder->addConditionalOption('className');
    }
}
