<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Number;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsRange implements Validator, Validator\Description
{
    /**
     * @var integer
     */
    private $min;

    /**
     * @var integer
     */
    private $max;

    /**
     * Set the min and max range of the field. Pass null to skip the check for the value.
     *
     * - Any number larger than and including 10
     *   new IsRange(10)
     *
     * - Any number less than and including 10
     *   new IsRange(null, 10)
     *
     * - A range between -50 and 50
     *   new IsRange(-50, 50)
     *
     * @param int $min
     * @param int $max
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = true;
        $isSet = ! ($value instanceof ValueWasNotSet);

        if ($isSet) {
            if (!is_null($this->min) && $this->min > $value) {
                // too long
                $isValid = false;
            }

            if ($isValid && !is_null($this->max) && $this->max < $value) {
                $isValid = false;
            }
        }

        return new Result(
            $isValid,
            $value,
            'NumberIsRange',
            (! $isValid ? 'Value is not in range' : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
