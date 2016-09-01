<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\GetAllowedEmptyTypes;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsInteger implements Validator, GetAllowedEmptyTypes, Validator\Description
{
    /**
     * @var bool
     */
    private $strict;

    /**
     * IsString constructor.
     *
     * @param bool $strict Strict validation? In which case we won't allow strings or floats as ints
     */
    public function __construct($strict = false)
    {
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function getAllowedEmptyTypes() : array
    {
        return ($this->strict ? [0] : [0, '0']);
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        // if the value wasn't set we should pass and allow isRequired to catch any errors
        $isValid = ($value instanceof ValueWasNotSet);

        if (! $isValid) {
            $isValid = ($this->strict
                ? is_int($value)
                : is_numeric($value) && (bool)preg_match('/^[-]{0,1}[0-9]{1,20}$/', $value, $results)
            );

            $value = (int)$value;
        }

        return new Result(
            $isValid,
            $value,
            'isInteger',
            (! $isValid ? 'Value is not a valid int' : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [
            'strict' => $this->strict,
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
