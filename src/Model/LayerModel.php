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

namespace Netzmacht\Contao\Leaflet\Model;

use Contao\Model\Collection;

/**
 * Class LayerModel for the tl_leaflet_layer table.
 *
 * @property mixed|null id
 * @property mixed|null alias
 * @property mixed|null pointToLayer
 * @property mixed|null onEachFeature
 * @property mixed|null boundsMode
 *
 * @package Netzmacht\Contao\Leaflet\Model
 */
class LayerModel extends AbstractActiveModel
{
    public const PERMISSION_EDIT = 'edit';

    public const PERMISSION_CREATE = 'create';

    public const PERMISSION_DELETE = 'delete';

    public const PERMISSION_DATA = 'data';

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
    public static function findMultipleByTypes(array $types, $options = [])
    {
        if (empty($types)) {
            return null;
        }

        $options['column'] = [
            sprintf(
                'type IN (%s)',
                substr(str_repeat('?,', count($types)), 0, -1)
            ),
        ];

        $options['value']  = $types;
        $options['return'] = 'Collection';

        return static::find($options);
    }
}
