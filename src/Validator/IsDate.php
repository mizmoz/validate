<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use \DateTime;
use \DateTimeZone;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsDate implements Validator
{
    /**
     * @var string
     */
    private $format;

    /**
     * @var bool
     */
    private $setValueToDateTime;

    /**
     * IsDate constructor.
     *
     * @param string $format
     * @param bool $setValueToDateTime Should the value before set to the resolved DateTime object or left alone?
     */
    public function __construct(string $format = 'Y-m-d', bool $setValueToDateTime = true)
    {
        $this->format = $format;
        $this->setValueToDateTime = $setValueToDateTime;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet);

        if (! $isValid) {
            // resolve the date
            $dateTime = DateTime::createFromFormat($this->format, $value, new DateTimeZone('UTC'));

            // are the items the same?
            $isValid = ($dateTime && $dateTime->format($this->format) === $value);

            // update the value if the result is valid and setValue... is true
            $value = ($isValid && $this->setValueToDateTime ? $dateTime : $value);
        }

        return new Result(
            $isValid,
            $value,
            'isDate',
            (! $isValid ? 'Date must be in the format: ' . $this->format : '')
        );
    }
}
