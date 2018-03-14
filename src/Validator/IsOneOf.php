<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\GetAllowedPropertyValues;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsOneOf implements Validator, GetAllowedPropertyValues, Validator\Description
{
    /**
     * @var array
     */
    private $allowed;

    /**
     * IsOneOf constructor.
     *
     * @param array $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * Get the allowed types
     *
     * @return array
     */
    public function getAllowedPropertyValues() : array
    {
        return $this->allowed;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        // don't check non set values
        $isValid = ($value instanceof ValueWasNotSet);

        if (! $isValid) {
            foreach ($this->allowed as $key => $allowed) {
                $result = Validate::resolve($allowed, $key)->validate($value);

                if ($result->isValid()) {
                    $value = $result->getValue();
                    $isValid = true;
                    break;
                }
            }
        }

        return (new Result(
            $isValid,
            $value,
            'isOneOf',
            (! $isValid ? 'Value is not valid' : '')
        ))->setAllowedValues($this->getAllowedPropertyValues());
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        $allowed = [];
        foreach ($this->allowed as $shape) {
            if (is_scalar($shape)) {
                $allowed[] = $shape;
            } else {
                $description = Description::getDescriptionForShapes([$shape]);
                $allowed[] = current($description);
            }
        }

        return [
            'allowed' => $allowed,
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
