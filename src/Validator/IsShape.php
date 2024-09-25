<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\ResultContainer;
use Mizmoz\Validate\Validator\Helper\ArrayAccess;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValidateIterableShapeTrait;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

/**
 * Class IsShape
 *
 * Like IsArrayOfShape except it will accept objects also so good for dealing with JSON etc when you might not
 * have control over something being an array or object.
 *
 * @package Mizmoz\Validate\Validator
 */
class IsShape implements Validator, Validator\Description
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
     * @return IsShape
     */
    public function addShape($key, $validator) : IsShape
    {
        $this->shape[$key] = $validator;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $resultContainer = new ResultContainer('isShape');

        if ($value instanceof ValueWasNotSet) {
            // no valid value was passed, but we're happy to say we've passed - let any required validator catch this
            $result = new Result(true, $value, 'isShape');
        } else {
            // check the value is iterable
            $result = (new IsIterable())->validate($value);
        }

        // add the result to the container
        $resultContainer->addResult($result);

        // get the value
        $value = $resultContainer->getValue();

        $values = ($result->isValid() ? $value : []);
        if (! is_array($values) && ! $values instanceof \ArrayAccess) {
            $values = new ArrayAccess($value);
        }

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
    public function jsonSerialize(): mixed
    {
        return $this->getDescription();
    }
}
