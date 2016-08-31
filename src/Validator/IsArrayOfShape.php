<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\ResultContainer;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsArrayOfShape implements Validator, Validator\Description
{
    /**
     * @var array
     */
    private $shape;

    /**
     * IsArrayOf constructor.
     *
     * @param array $shape
     */
    public function __construct(array $shape)
    {
        $this->shape = $shape;
    }

    /**
     * Add a shape to the validator
     *
     * @param $key
     * @param $validator
     * @return IsArrayOfShape
     */
    public function addShape($key, $validator) : IsArrayOfShape
    {
        $this->shape[$key] = $validator;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $resultContainer = new ResultContainer('isArrayOfShape');

        $resultContainer->addResult((new IsArray())->validate($value));
        $value = $resultContainer->getValue();
        $values = (is_array($value) ? $value : []);

        foreach ($this->shape as $key => $shape) {
            // get the value for the key
            $keyValue = (array_key_exists($key, $values) ? $values[$key] : new ValueWasNotSet);

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

    /**
     * @inheritdoc
     */
    public function getDescription() : array
    {
        return Description::getDescriptionForShapes($this->shape);
    }
}
