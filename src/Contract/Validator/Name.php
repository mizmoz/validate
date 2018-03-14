<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract\Validator;

/**
 * Get the validator name
 *
 * @package Mizmoz\Validate\Contract\Validator
 */
interface Name
{
    /**
     * Get the validator name, should be lower case first camel cased like isArrayOf
     *
     * @return string
     */
    public function getName(): string;
}
