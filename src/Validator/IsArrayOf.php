<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as  ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsArrayOf implements Validator, Validator\Description
{
    /**
     * @var Validator
     */
    private $allowed;

    /**
     * IsArrayOf constructor.
     *
     * @param Validator $allowed
     */
    public function __construct(Validator $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        if ($value instanceof ValueWasNotSet) {
            $isValid = true;
        } else {
            $result = (new IsArray())->validate($value);
            $isValid = $result->isValid();
            $value = $result->getValue();

            if ($isValid) {
                foreach ($value as $v) {
                    if (! Validate::resolve($this->allowed)->validate($v)->isValid()) {
                        $isValid = false;
                        break;
                    }
                }
            }
        }

        return new Result(
            $isValid,
            $value,
            'isArrayOf',
            (! $isValid ? 'Value is not valid' : '')
        );
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return [
            Description::getName($this->allowed) => Description::getValidationDescription($this->allowed)
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
