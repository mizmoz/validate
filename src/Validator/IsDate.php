<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Validator\Helper\ConstructorWithOptionsTrait;
use Mizmoz\Validate\Validator\Helper\Date;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsDate implements Validator, Validator\Description
{
    use ConstructorWithOptionsTrait;

    /**
     * @inheritDoc
     */
    public static function getDefaultOptions(array $options): array
    {
        return [
            // The date format
            'format' => 'Y-m-d',

            // Should the value before set to the resolved Date object or left alone?
            'setValueToDateTime' => true,

            // Using strict will treat an empty string as a failure
            'strict' => true,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet);

        if (! $isValid && ! $this->option('strict') && $value === '') {
            // value is not actually set
            $value = new ValueWasNotSet();
            $isValid = true;
        }

        if (! $isValid && ! is_null($value)) {
            // resolve the date
            $date = Date::create($this->option('format'), $value);

            // are the items the same?
            $isValid = ($date && $date->format($this->option('format')) === $value);

            // update the value if the result is valid and setValue... is true
            $value = ($isValid && $this->option('setValueToDateTime') ? $date : $value);
        }

        return new Result(
            $isValid,
            $value,
            'isDate',
            (! $isValid ? 'Date must be in the format: ' . $this->option('format') : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        // @todo Add PHP => moment.js date format conversion, probably in a resolver
        return [
            'format' => $this->option('format'),
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
