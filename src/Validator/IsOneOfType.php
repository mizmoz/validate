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

class IsOneOfType implements Validator
{
    /**
     * @var Validator[]
     */
    private $allowed;

    /**
     * IsString constructor.
     *
     * @param Validator[] $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = false;
        foreach ($this->allowed as $validator) {
            if ($validator->validate($value)->isValid()) {
                $isValid = true;
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
}
