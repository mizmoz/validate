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
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsRequired implements Validator, Validator\Description
{
    /**
     * @var array
     */
    private $allowedEmptyTypes;

    /**
     * IsRequired constructor.
     *
     * @param array $allowedEmptyTypes
     */
    public function __construct(array $allowedEmptyTypes = [])
    {
        $this->allowedEmptyTypes = $allowedEmptyTypes;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = (! $value instanceof ValueWasNotSet
            && ($value || in_array($value, $this->allowedEmptyTypes, true)));

        return new Result(
            $isValid,
            $value,
            'isRequired',
            (! $isValid ? 'Value is required' : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
