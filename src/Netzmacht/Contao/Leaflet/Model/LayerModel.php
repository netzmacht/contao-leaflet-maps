<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Model;

use Model\Collection;

/**
 * Class LayerModel for the tl_leaflet_layer table.
 *
 * @property mixed|null id
 * @property mixed|null alias
 * @property mixed|null pointToLayer
 * @property mixed|null onEachFeature
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class LayerModel extends AbstractActiveModel
{
    /**
     * Model table.
     *
     * @var string
     */
    protected static $strTable = 'tl_leaflet_layer';

    /**
     * Find multiple layers by given type.
     *
     * @param array $types   List of layer types.
     * @param array $options Query options.
     *
     * @return Collection|null
     */
    public static function findMultipleByTypes(array $types, $options = array())
    {
        if (empty($types)) {
            return null;
        }

        $options['column'] = array(
            sprintf(
                'type IN (%s)',
                substr(str_repeat('?,', count($types)), 0, -1)
            )
        );

        $options['value']  = $types;
        $options['return'] = 'Collection';

        return static::find($options);
    }
}
