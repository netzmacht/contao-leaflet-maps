<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Leaflet\Dca;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Contao\Toolkit\Dca\Callback\CallbackFactory;
use Netzmacht\LeafletPHP\Value\LatLng;

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
     * Validator constructor.
     *
     * @param Translator $translator Translator.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Generate the callback definition.
     *
     * @param string $methodName Callback method name.
     *
     * @return callable
     */
    public static function callback($methodName)
    {
        return CallbackFactory::service('leaflet.dca.validator', $methodName);
    }

    /**
     * Validate coordinates.
     *
     * @param mixed $value Given value.
     *
     * @return mixed
     */
    public function validateCoordinates($value)
    {
        try {
            LatLng::fromString($value);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                $this->translator->translate('invalidCoordinates', 'leaflet', [$value]),
                0,
                $e
            );
        }

        return $value;
    }

    /**
     * Validate multiple coordinates.
     *
     * @param mixed $values Given value.
     *
     * @return mixed
     */
    public function validateMultipleCoordinates($values)
    {
        if (!is_array($values)) {
            $lines = explode("\n", $values);
        } else {
            $lines = $values;
        }

        foreach ($lines as $coordinate) {
            $this->validateCoordinates($coordinate);
        }

        return $values;
    }

    /**
     * Validate multiple coordinate sets.
     *
     * @param mixed $values Given value.
     *
     * @return mixed
     */
    public function validateMultipleCoordinateSets($values)
    {
        $sets = deserialize($values, true);
        foreach ($sets as $lines) {
            $this->validateMultipleCoordinates($lines);
        }

        return $values;
    }

    /**
     * Validate an alias.
     *
     * @param string $value Given value
     *
     * @return string
     * @throws \InvalidArgumentException When invalid value given.
     */
    public function validateAlias($value)
    {
        if (preg_match('/^[A-Za-z_]+[A-Za-z0-9_]+$/', $value) !== 1) {
            throw new \InvalidArgumentException(
                $this->translator->translate('invalidAlias', 'leaflet')
            );
        }

        return $value;
    }
}
