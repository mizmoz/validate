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
use Mizmoz\Validate\Validator\Helper\Description;

class IsOneOfType implements Validator, Validator\Description
{
    /**
     * @var Validator[]
     */
    private $allowed;

    /**
     * IsOneOfType constructor.
     *
     * @param Validator[] $allowed
     */
    public function __construct($allowed)
    {
        $this->allowed = (is_array($allowed) ? $allowed : [$allowed]);
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = false;
        foreach ($this->allowed as $validator) {
            $result = $validator->validate($value);

            if ($result->isValid()) {
                $isValid = true;
                $value = $result->getValue();
                break;
            }
        }

        return new Result(
            $isValid,
            $value,
            'isOneOfType',
            (! $isValid ? 'Value is not valid' : '')
        );
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Description::getDescriptionForShapes($this->allowed);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
