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


class LayerModel extends \Model
{
    protected static $strTable = 'tl_leaflet_layer';

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

        $options['value'] = $types;

        return static::find($options);
    }
}
