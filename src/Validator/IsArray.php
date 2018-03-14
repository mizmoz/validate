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
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsArray implements Validator
{
    /**
     * @var bool
     */
    private $strict;

    /**
     * IsString constructor.
     *
     * @param bool $strict Strict validation? In which case we won't try and resolve objects to arrays
     */
    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet || is_array($value));

        if (! $isValid
            && ! $this->strict
            && (new IsObject)->validate($value)->isValid()
            && method_exists($value, 'toArray')) {
            // object has a __toString method
            return $isValid = $this->validate($value->toArray());
        }

        return new Result(
            $isValid,
            $value,
            'isArray',
            (! $isValid ? 'Value is not a valid array' : '')
        );
    }
}
