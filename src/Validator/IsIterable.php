<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Result;
use \stdClass;
use \Traversable;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;

class IsIterable implements Validator
{
    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = (is_array($value)
            || $value instanceof stdClass
            || $value instanceof Traversable);

        return new Result(
            $isValid,
            $value,
            'isIterable',
            (! $isValid ? 'Value is not an iterable' : '')
        );
    }
}
