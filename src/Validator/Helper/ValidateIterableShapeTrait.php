<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Helper;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Exception\InvalidHelperTypeException;
use Mizmoz\Validate\ResultContainer;
use Mizmoz\Validate\Validate;

trait ValidateIterableShapeTrait
{
    /**
     * Validate the shapes
     *
     * @param array $shapes
     * @param mixed $values Must be iterable
     * @param ResultContainer $resultContainer
     * @return ResultContract
     */
    private function validateIterableShape(
        array $shapes,
        $values,
        ResultContainer $resultContainer
    ) : ResultContract {
        if (! is_array($shapes) && ! $shapes instanceof \ArrayAccess) {
            throw new InvalidHelperTypeException('$shapes must implement ArrayAccess');
        }

        foreach ($shapes as $key => $shape) {
            // get the value for the key - we accept that a null value in an object might
            // not be interpreted correctly
            $keyExists = $values instanceof \ArrayAccess ? $values->offsetExists($key) : array_key_exists($key, $values);
            $keyValue = $keyExists
                ? $values[$key] : new ValueWasNotSet();

            // get the result
            $result = Validate::resolve($shape, $key)->validate($keyValue);

            if (! $result->getValue() instanceof ValueWasNotSet) {
                // update the original value
                $values[$key] = $result->getValue();
            }

            // validate and add the result to the result container
            $resultContainer->addResult($result, $key);
        }

        // update the values and return
        return $resultContainer->setValue($values);
    }
}
