<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\GetAllowedEmptyTypes;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Type\Decimal;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

/**
 * Class IsDecimal
 *
 * Check if a string is a decimal. We'll reject all floats as these are dangerous to work with.
 *
 * @package Mizmoz\Validate\Validator
 */
class IsDecimal implements Validator, GetAllowedEmptyTypes, Validator\Description
{
    /**
     * @var int
     */
    private $decimalPlaces;

    /**
     * IsDecimal constructor.
     * @param int $decimalPlaces
     */
    public function __construct(int $decimalPlaces = 2)
    {
        $this->decimalPlaces = $decimalPlaces;
    }

    /**
     * @inheritdoc
     */
    public function getAllowedEmptyTypes() : array
    {
        return [
            '0.' . str_repeat('0', $this->decimalPlaces),
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = $value instanceof ValueWasNotSet;

        if (! $isValid) {
            // check the decimal places are correct
            if (is_numeric($value) && preg_match('/(-)?[0-9]+\.[0-9]{' . $this->decimalPlaces . '}$/i', $value)) {
                // valid so convert the string to a decimal type
                $isValid = true;
                $value = new Decimal($value, $this->decimalPlaces);
            }
        }

        return new Result(
            $isValid,
            $value,
            'isDecimal',
            (! $isValid ? 'Value is not a valid decimal' : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [
            'decimalPlaces' => $this->decimalPlaces,
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->getDescription();
    }
}
