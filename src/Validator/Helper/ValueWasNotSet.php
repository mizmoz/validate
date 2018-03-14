<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Helper;

/**
 * This is just used as a way of passing something to a validator to say the valud was not set.
 *
 * For example in the IsArrayOfShape validator we might have a non required field IsString but passing null will make
 * the validator fail the test. Passing ValueWasNotSet allows IsString to handle the validation how it sees fit.
 *
 * @package Mizmoz\Validate\Validator\Helper
 */
class ValueWasNotSet
{
}
