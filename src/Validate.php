<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Exception\RuntimeException;
use Mizmoz\Validate\Validator\IsSame;
use Mizmoz\Validate\Validator\IsShape;

/**
 * Class Validate
 * @package Mizmoz\Validate
 *
 * @method static Chain isArray(bool $strict = false)
 * @method static Chain isArrayOf($allowed)
 * @method static Chain isArrayOfShape(array $shape)
 * @method static Chain isArrayOfType($allowed)
 * @method static Chain isBoolean()
 * @method static Chain isDate(array $options = [])
 * @method static Chain isDecimal(int $decimalPlaces = 2)
 * @method static Chain isEmail(array $options = [])
 * @method static Chain isEmailDisposable()
 * @method static Chain isFilter(array $tags = [])
 * @method static Chain isInteger(bool $strict = false)
 * @method static Chain isIterable()
 * @method static Chain isNumeric()
 * @method static Chain isObject()
 * @method static Chain isOneOf(array $allowed)
 * @method static Chain isOneOfType(array $allowed)
 * @method static Chain isReCaptcha(string $secret, string $remoteIp = '', string $url = 'https://www.google.com/recaptcha/api/siteverify')
 * @method static Chain isRequired(Validator $validator, $allowedEmptyTypes = null)
 * @method static Chain isSame($match, bool $strict = false)
 * @method static Chain isShape(array $allowed)
 * @method static Chain isString(bool $strict = false)
 */
class Validate
{
    /**
     * @param string $name
     * @param array $arguments
     * @return Chain
     */
    public static function __callStatic(string $name, array $arguments = []) : Chain
    {
        return new Chain(
            call_user_func_array("Mizmoz\\Validate\\ValidatorFactory::$name", $arguments)
        );
    }

    /**
     * Helper as it's easier than typing IsShape. Use for things like validating key => value pair like:
     * Validate::set(['name' => Validate::isString()->isRequired, 'age' => Validate::isInteger()])
     *
     * @param array $allowed
     * @return IsShape
     */
    public static function set(array $allowed) : IsShape
    {
        return ValidatorFactory::isShape($allowed);
    }

    /**
     * Resolve the $value and $key to a Validator
     *
     * @param $value
     * @param null $key
     * @return Validator|Chain
     */
    public static function resolveToValidator($value, $key = null)
    {
        if (is_int($key) && ! $value instanceof Validator) {
            return new IsSame($value);
        }

        if ($value instanceof Validator) {
            return $value;
        }

        if ($value instanceof Chain) {
            return $value;
        }

        throw new RuntimeException('NOT IMPLEMENTED YET');
    }

    /**
     * Resolve the validator to a Chain
     *
     * @param $value
     * @param null $key
     * @return Chain
     */
    public static function resolve($value, $key = null) : Chain
    {
        if (is_int($key) && ! $value instanceof Validator) {
            return new Chain(new IsSame($value, true));
        }

        if ($value instanceof Validator) {
            return new Chain($value);
        }

        if ($value instanceof Chain) {
            return $value;
        }

        throw new RuntimeException('NOT IMPLEMENTED YET');
    }
}
