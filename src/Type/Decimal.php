<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Type;

class Decimal implements \JsonSerializable
{
    /**
     * @var int
     */
    private $integerPart;

    /**
     * @var int
     */
    private $fractionalPart;

    /**
     * @var int
     */
    private $decimalPlaces;

    /**
     * Decimal constructor.
     *
     * @param string $amount
     * @param int $decimalPlaces
     */
    public function __construct($amount = '0', int $decimalPlaces = 2)
    {
        $split = explode('.', $amount);
        $this->integerPart = (int)$split[0];
        $this->decimalPlaces = $decimalPlaces;

        $part = substr((isset($split[1]) ?  $split[1] : 0), 0, $decimalPlaces);
        $this->fractionalPart = (int)str_pad($part, $this->decimalPlaces, 0);
    }

    /**
     * Create the Decimal object from cents
     *
     * @param $cents
     * @param int $decimalPlaces
     * @return Decimal
     */
    public static function fromCents($cents, int $decimalPlaces = 2): Decimal
    {
        $value = (string)$cents;
        $value = str_pad($value, $decimalPlaces + 1, 0, STR_PAD_LEFT);
        $value = preg_replace('/^([0-9]*)([0-9]{' . $decimalPlaces . '})$/', '\\1.\\2', $value);
        return new Decimal($value, $decimalPlaces);
    }

    /**
     * Get the number of decimal places
     *
     * @return int
     */
    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }

    /**
     * Get the integer part of the decimal
     *
     * @return int
     */
    public function getInteger(): int
    {
        return $this->integerPart;
    }

    /**
     * Get the fractional part of the decimal
     *
     * @return int
     */
    public function getFractional(): int
    {
        return $this->fractionalPart;
    }

    /**
     * Get the fractional padded with zeros
     *
     * @return string
     */
    public function getFractionalAsString(): string
    {
        return str_pad($this->fractionalPart, $this->decimalPlaces, 0, STR_PAD_LEFT);
    }

    /**
     * Get the total cents
     *
     * @return int
     */
    public function getCents(): int
    {
        return (int)str_replace('.', '', $this->__toString());
    }

    /**
     * Get the float value, kinda goes against the point of this
     *
     * @return float
     */
    public function getFloatValue(): float
    {
        return (float)($this->integerPart . '.' . $this->getFractionalAsString());
    }

    /**
     * Check decimal values are OK
     *
     * @param Decimal $decimal
     * @throws \DomainException
     */
    private function isItSafe(Decimal $decimal)
    {
        if ($this->decimalPlaces !== $decimal->getDecimalPlaces()) {
            throw new \DomainException('Decimal values are not the same. They must share the same number of decimal places');
        }
    }

    /**
     * Add decimal to the current value and return a new Decimal instance
     *
     * @param Decimal $decimal
     * @return Decimal
     */
    public function addDecimal(Decimal $decimal): Decimal
    {
        $this->isItSafe($decimal);

        $value = $this->getCents() + $decimal->getCents();
        $value = str_pad($value, $this->decimalPlaces + 1, 0, STR_PAD_LEFT);
        $value = preg_replace('/^([0-9]*)([0-9]{' . $this->decimalPlaces . '})$/', '\\1.\\2', $value);

        return new self($value, $this->decimalPlaces);
    }

    /**
     * Subtract decimal from the current value and return a new Decimal instance
     *
     * @param Decimal $decimal
     * @return Decimal
     */
    public function subtractDecimal(Decimal $decimal): Decimal
    {
        $this->isItSafe($decimal);

        $value = $this->getCents() - $decimal->getCents();
        $value = str_pad($value, $this->decimalPlaces + 1, 0, STR_PAD_LEFT);
        $value = preg_replace('/^([0-9]*)([0-9]{' . $this->decimalPlaces . '})$/', '\\1.\\2', $value);

        return new self($value, $this->decimalPlaces);
    }

    /**
     * Get a percentage of the decimal
     *
     * @param $percent
     * @return Decimal
     */
    public function getPercent($percent): Decimal
    {
        $value = round($this->getCents() * ($percent / 100)) / 100;
        return new Decimal($value, $this->decimalPlaces);
    }

    /**
     * Add a percent from the decimal
     *
     * @param $percent
     * @return Decimal
     */
    public function addPercent($percent): Decimal
    {
        return $this->addDecimal($this->getPercent($percent));
    }

    /**
     * Subtract a percent from the decimal
     *
     * @param $percent
     * @return Decimal
     */
    public function subtractPercent($percent): Decimal
    {
        return $this->subtractDecimal($this->getPercent($percent));
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /**
     * Get the decimal as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->integerPart . ($this->decimalPlaces ? '.' . $this->getFractionalAsString() : '');
    }
}