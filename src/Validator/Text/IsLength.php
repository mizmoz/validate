<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Text;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsLength implements Validator, Validator\Description
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
     * @var null|string
     */
    private $encoding;

    /**
     * Set the min and max length of the field
     *
     * @param int $min
     * @param int $max
     * @param string $encoding
     */
    public function __construct($min = 0, $max = 0, $encoding = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->encoding = ($encoding ? $encoding : mb_internal_encoding());
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = true;
        $isSet = ! ($value instanceof ValueWasNotSet);

        if ($isSet) {
            $length = mb_strlen($value, $this->encoding);

            if ($this->min && $this->min > $length) {
                // too long
                $isValid = false;
            }

            if ($isValid && $this->max && $this->max < $length) {
                $isValid = false;
            }
        }

        return new Result(
            $isValid,
            $value,
            'stringLength',
            (! $isValid ? 'Value is not the correct length' : '')
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
