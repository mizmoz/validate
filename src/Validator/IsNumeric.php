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

class IsNumeric implements Validator, GetAllowedEmptyTypes
{
    /**
     * @inheritdoc
     */
    public function getAllowedEmptyTypes() : array
    {
        return [0, '0'];
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet || is_numeric($value));

        return new Result(
            $isValid,
            $value,
            'isNumeric',
            (! $isValid ? 'Value is not a valid number' : '')
        );
    }
}
