<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract\Validator;

interface ToInteger
{
    /**
     * Return the object as an integer, this is checked when using the isInteger validator
     * and is useful for adding to things like models to return it's primary key etc.
     *
     * @return int
     */
    public function toInteger(): int;
}