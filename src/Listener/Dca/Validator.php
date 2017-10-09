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

namespace Netzmacht\Contao\Leaflet\Listener\Dca;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\LeafletPHP\Value\LatLng;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class Validator.
 *
 * @package Netzmacht\Contao\Leaflet\Dca
 */
class Validator
{
    /**
     * Translator.
     *
     * @var Translator;
     */
    private $translator;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * Validator constructor.
     *
     * @param Manager    $dcaManager Data container manager.
     * @param Translator $translator Translator.
     */
    public function __construct(Manager $dcaManager, Translator $translator)
    {
        $this->translator = $translator;
        $this->dcaManager = $dcaManager;
    }

    /**
     * Validate coordinates.
     *
     * @param mixed         $value         Given value.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException When invalid coordinates given.
     */
    public function validateCoordinates($value, $dataContainer)
    {
        if (!$value && !$this->isRequired($dataContainer)) {
            return $value;
        }

        try {
            LatLng::fromString($value);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                $this->translator->trans('invalidCoordinates', [$value], 'contao_leaflet'),
                0,
                $e
            );
        }

        return $value;
    }

    /**
     * Validate multiple coordinates.
     *
     * @param mixed         $values        Given value.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException When invalid coordinates given.
     */
    public function validateMultipleCoordinates($values, $dataContainer)
    {
        if (!is_array($values)) {
            $lines = explode("\n", $values);
        } else {
            $lines = $values;
        }

        foreach ($lines as $coordinate) {
            $this->validateCoordinates($coordinate, $dataContainer);
        }

        return $values;
    }

    /**
     * Validate multiple coordinate sets.
     *
     * @param mixed         $values        Given value.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException When invalid coordinates given.
     */
    public function validateMultipleCoordinateSets($values, $dataContainer)
    {
        $sets = deserialize($values, true);
        foreach ($sets as $lines) {
            $this->validateMultipleCoordinates($lines, $dataContainer);
        }

        return $values;
    }

    /**
     * Validate an alias.
     *
     * @param string $value Given value.
     *
     * @return string
     * @throws \InvalidArgumentException When invalid value given.
     */
    public function validateAlias($value)
    {
        if (preg_match('/^[A-Za-z_]+[A-Za-z0-9_]+$/', $value) !== 1) {
            throw new \InvalidArgumentException(
                $this->translator->trans('invalidAlias', [], 'contao_leaflet')
            );
        }

        return $value;
    }

    /**
     * Check if value is required.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return bool
     */
    private function isRequired($dataContainer): bool
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        if ($definition->get(['fields', $dataContainer->field, 'eval', 'mandatory'])) {
            return true;
        }

        return false;
    }
}
