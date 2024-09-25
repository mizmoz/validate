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

class IsString implements Validator, Validator\Description
{
    /**
     * @var bool
     */
    private $strict;

    /**
     * IsString constructor.
     *
     * @param bool $strict Strict validation? In which case we won't try and resolve objects to string
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
        $isValid = ($value instanceof ValueWasNotSet || is_string($value));

        if (! $isValid
            && ! $this->strict
            && (new IsObject)->validate($value)->isValid()
            && method_exists($value, '__toString')) {
            // object has a __toString method
            return $isValid = $this->validate((string)$value);
        }

        return new Result(
            $isValid,
            $value,
            'isString',
            (! $isValid ? 'Value is not a valid string' : '')
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
    public function jsonSerialize(): mixed
    {
        return $this->getDescription();
    }
}
