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
use Mizmoz\Validate\ResultContainer;
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
        $resultContainer = new ResultContainer('isArrayOf');

        if ($value instanceof ValueWasNotSet) {
            // empty but this is valid unless the field is required
            $resultContainer->addResult(new Result(
                true,
                $value,
                'isArrayOf'
            ));
        } else {
            $result = (new IsArray())->validate($value);
            $resultContainer->addResult($result);
            $value = $result->getValue();

            if ($resultContainer->isValid()) {
                foreach ($value as $v) {
                    $result = Validate::resolve($this->allowed)->validate($v);
                    $resultContainer->addResult($result);

                    if (! $resultContainer->isValid()) {
                        break;
                    }
                }
            }
        }

        return $resultContainer->setValue($value);
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
