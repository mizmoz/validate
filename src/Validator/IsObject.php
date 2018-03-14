<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;

class IsObject implements Validator
{
    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = is_object($value);

        return new Result(
            $isValid,
            $value,
            'isObject',
            (! $isValid ? 'Value is not a valid object' : '')
        );
    }
}
