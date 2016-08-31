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
use Mizmoz\Validate\Validate;

class IsArrayOf implements Validator
{
    /**
     * @var mixed
     */
    private $allowed;

    /**
     * IsArrayOf constructor.
     *
     * @param mixed $allowed
     */
    public function __construct($allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $result = (new IsArray())->validate($value);
        $isValid = $result->isValid();
        $value = $result->getValue();

        if ($isValid) {
            foreach ($value as $v) {
                if (! Validate::resolve($this->allowed)->validate($v)->isValid()) {
                    $isValid = false;
                    break;
                }
            }
        }

        return new Result(
            $isValid,
            $value,
            'isArrayOf',
            (! $isValid ? 'Value is not valid' : '')
        );
    }
}
