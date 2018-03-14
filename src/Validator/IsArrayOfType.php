<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as  ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\ResultContainer;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsArrayOfType implements Validator, Validator\Description, Validator\Name
{
    /**
     * @var Validator[]
     */
    private $allowed;

    /**
     * @var IsOneOfType
     */
    private $validator;

    /**
     * IsArrayOfType constructor.
     *
     * @param Validator[] $allowed
     */
    public function __construct($allowed)
    {
        $this->allowed = $allowed;

        // create isOneOfType validator
        $this->validator = new IsOneOfType($this->allowed);
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $resultContainer = new ResultContainer($this->getName());

        if ($value instanceof ValueWasNotSet) {
            // empty but this is valid unless the field is required
            $resultContainer->addResult(new Result(
                true,
                $value,
                $this->getName()
            ));
        } else {
            $result = (new IsArray())->validate($value);
            $resultContainer->addResult($result);
            $value = $result->getValue();

            if ($resultContainer->isValid()) {
                foreach ($value as &$v) {
                    $result = $this->validator->validate($v);
                    $resultContainer->addResult($result);

                    if (! $resultContainer->isValid()) {
                        break;
                    }

                    // valid so update the value in case we've resolved it to something else
                    $v = $result->getValue();
                }
            }
        }

        return $resultContainer->setValue($value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'isArrayOfType';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->validator->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
