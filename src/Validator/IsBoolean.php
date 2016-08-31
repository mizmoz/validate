<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\GetAllowedPropertyValues;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Resolver\ToMappedValue;

class IsBoolean implements Validator, GetAllowedPropertyValues
{
    /**
     * @var array
     */
    private $allowed = [0, 1, '0', '1', true, false, 'true', 'false'];

    /**
     * Get the allowed types
     *
     * @return array
     */
    public function getAllowedPropertyValues() : array
    {
        return $this->allowed;
    }

    public function getMappedPropertyValues() : array
    {
        return [
            [
                'from' => [true, 'true', 1, '1'],
                'to' => true,
            ],
            [
                'from' => [false, 'false', 0, '0'],
                'to' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $result = (new IsOneOf($this->allowed))->validate($value);

        if ($result->isValid()) {
            $newValue = (new ToMappedValue($this->getMappedPropertyValues()))->resolve($result->getValue());

            // set the net value
            $result->setValue($newValue);
        }

        return $result;
    }
}
