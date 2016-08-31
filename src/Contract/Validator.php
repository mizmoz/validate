<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract;

interface Validator
{
    /**
     * Validate the value and return a result object
     *
     * @param $value
     * @return Result
     */
    public function validate($value) : Result;
}
