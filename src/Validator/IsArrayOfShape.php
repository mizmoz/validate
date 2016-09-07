<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\ResultContainer;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValidateIterableShapeTrait;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsArrayOfShape implements Validator, Validator\Description
{
    use ValidateIterableShapeTrait;

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

        if ($value instanceof ValueWasNotSet) {
            // no valid value was passed, but we're happy to say we've passed - let any required validator catch this
            $result = new Result(true, $value, 'isArrayOfShape');
        } else {
            // check the value is an array
            $result = (new IsArray())->validate($value);
        }

        $resultContainer->addResult($result);
        $value = $resultContainer->getValue();
        $values = (is_array($value) || $value instanceof \ArrayAccess ? $value : []);

        return $this->validateIterableShape($this->shape, $values, $resultContainer);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Description::getDescriptionForShapes($this->shape);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
