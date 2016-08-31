<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use Mizmoz\Validate\Contract\GetAllowedEmptyTypes;
use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Exception\NonExistentHelperException;
use Mizmoz\Validate\Exception\NonUniqueHelperNameException;
use Mizmoz\Validate\Exception\InvalidHelperTypeException;
use Mizmoz\Validate\Validator\IsArray;
use Mizmoz\Validate\Validator\IsArrayOf;
use Mizmoz\Validate\Validator\IsArrayOfShape;
use Mizmoz\Validate\Validator\IsBoolean;
use Mizmoz\Validate\Validator\IsInteger;
use Mizmoz\Validate\Validator\IsNumeric;
use Mizmoz\Validate\Validator\IsObject;
use Mizmoz\Validate\Validator\IsOneOf;
use Mizmoz\Validate\Validator\IsOneOfType;
use Mizmoz\Validate\Validator\IsRequired;
use Mizmoz\Validate\Validator\IsSame;
use Mizmoz\Validate\Validator\IsString;
use Mizmoz\Validate\Validator\StringLength;

class ValidatorFactory
{
    /**
     * Validator namespace
     */
    const VALIDATOR_NAMESPACE = __NAMESPACE__ . '\\Validator\\';

    /**
     * Resolver namespace
     */
    const RESOLVER_NAMESPACE = __NAMESPACE__ . '\\Resolver\\';

    /**
     * Allow overriding the default helpers or setting new ones
     *
     * @var array
     */
    private static $helper = [
        'isBoolean' => self::VALIDATOR_NAMESPACE . 'IsBoolean',
        'isEmail' => self::VALIDATOR_NAMESPACE . 'IsEmail',
        'isFilter' => self::VALIDATOR_NAMESPACE . 'IsFilter',

        // resolvers
        'toClass' => self::RESOLVER_NAMESPACE . 'ToClass',
        'toDefaultValue' => self::RESOLVER_NAMESPACE . 'ToDefaultValue',
        'toMappedValue' => self::RESOLVER_NAMESPACE . 'ToMappedValue',
        'toModel' => self::RESOLVER_NAMESPACE . 'ToModel',
        'toStdClass' => self::RESOLVER_NAMESPACE . 'ToStdClass',
        'toValue' => self::RESOLVER_NAMESPACE . 'ToValue',
    ];

    /**
     * Get the valid helper types
     *
     * @return array
     */
    public static function getValidHelperTypes() : array
    {
        return [
            'Mizmoz\Validate\Contract\Resolver',
            'Mizmoz\Validate\Contract\Validator',
        ];
    }

    /**
     * Does a helper exist by the given name?
     *
     * @param string $name
     * @return bool
     */
    public static function helperExists(string $name) : bool
    {
        return (method_exists(get_called_class(), $name) || array_key_exists($name, self::$helper));
    }

    /**
     * Set a helper by class name
     *
     * @param string $name
     * @param string $class
     * @param bool $mustBeUnique
     * @throws InvalidHelperTypeException
     */
    public static function setHelperClass(string $name, string $class, bool $mustBeUnique = true)
    {
        $implements = class_implements($class);

        $callback = null;
        $callbackWrapper = function ($class, $arguments) {
            $reflection = new \ReflectionClass($class);
            return $reflection->newInstanceArgs($arguments);
        };

        if (in_array('Mizmoz\Validate\Contract\Resolver', $implements)) {
            $callback = function () use ($class, $callbackWrapper) : Resolver {
                return $callbackWrapper($class, func_get_args());
            };
        } else if (in_array('Mizmoz\Validate\Contract\Validator', $implements)) {
            $callback = function () use ($class, $callbackWrapper) : Validator {
                return $callbackWrapper($class, func_get_args());
            };
        } else {
            throw new InvalidHelperTypeException(
                '$class must implement: ' .
                implode(', ', self::getValidHelperTypes())
            );
        }

        return static::setHelper($name, $callback, $mustBeUnique);
    }

    /**
     * Set multiple helpers from an array
     *
     * @param array $helpers
     * @param bool $mustBeUnique
     */
    public static function setHelperClasses(array $helpers, bool $mustBeUnique = true)
    {
        foreach ($helpers as $name => $class) {
            static::setHelperClass($name, $class, $mustBeUnique);
        }
    }

    /**
     * Set the helper, name must be unique otherwise it will get overwritten. The helper must return a Validate or
     * Resolve contract
     *
     * @param string $name
     * @param callable $method
     * @param bool $mustBeUnique
     * @throws NonUniqueHelperNameException
     * @throws InvalidHelperTypeException
     */
    public static function setHelper(string $name, callable $method, bool $mustBeUnique = true)
    {
        if ($mustBeUnique && array_key_exists($name, self::$helper)) {
            throw (new NonUniqueHelperNameException())
                ->setHelperName($name);
        }

        // check the callback will return a valid item
        $reflection = new \ReflectionFunction($method);

        if (! in_array((string)$reflection->getReturnType(), self::getValidHelperTypes())) {
            throw new InvalidHelperTypeException(
                '$method does not return a valid helper type. Please only use: ' .
                    implode(', ', self::getValidHelperTypes())
            );
        }

        // set the helper
        self::$helper[$name] = $method;
    }

    /**
     * Resolve the helper or return false
     *
     * @param string $name
     * @param array $arguments
     * @return Validator|Resolver|Chain|false
     */
    public static function resolveHelper(string $name, array $arguments = [])
    {
        if (isset(self::$helper[$name])) {
            if (! is_callable(self::$helper[$name])) {
                static::setHelperClass($name, self::$helper[$name], false);
            }

            return call_user_func_array(self::$helper[$name], $arguments);
        }

        return false;
    }

    /**
     * Return a new IsArray validator
     *
     * @param bool $strict
     * @return IsArray
     */
    public static function isArray(bool $strict = false) : IsArray
    {
        return new IsArray($strict);
    }

    /**
     * Return a new IsArrayOf validator
     *
     * @param mixed $allowed
     * @return IsArrayOf
     */
    public static function isArrayOf($allowed) : IsArrayOf
    {
        return new IsArrayOf($allowed);
    }

    /**
     * Return a new IsArrayOfShape validator
     *
     * @param array $shape
     * @return IsArrayOfShape
     */
    public static function isArrayOfShape(array $shape) : IsArrayOfShape
    {
        return new IsArrayOfShape($shape);
    }

    /**
     * Return an IsBoolean validator chain
     *
     * @return IsBoolean
     */
    public static function isBoolean() : IsBoolean
    {
        return static::resolveHelper('isBoolean');
    }

    /**
     * Return a new IsInteger validator
     *
     * @param bool $strict
     * @return IsInteger
     */
    public static function isInteger(bool $strict = false) : IsInteger
    {
        return new IsInteger($strict);
    }

    /**
     * Is a number of some kind. This can be an int, float or string that looks like a number
     *
     * @return IsNumeric
     */
    public static function isNumeric() : IsNumeric
    {
        return new IsNumeric();
    }

    /**
     * Return a new IsObject validator
     *
     * @return IsObject
     */
    public static function isObject() : IsObject
    {
        return new IsObject();
    }

    /**
     * Return a new IsOneOf validator
     *
     * @param array $allowed
     * @return IsOneOf
     */
    public static function isOneOf(array $allowed) : IsOneOf
    {
        return new IsOneOf($allowed);
    }

    /**
     * Return a new IsOneOfType validator
     *
     * @param array $allowed
     * @return IsOneOfType
     */
    public static function isOneOfType(array $allowed) : IsOneOfType
    {
        return new IsOneOfType($allowed);
    }

    /**
     * Set the required flag
     *
     * @param Validator $validator
     * @param array $allowedEmptyTypes
     * @return IsRequired
     */
    public static function isRequired(Validator $validator, $allowedEmptyTypes = null) : IsRequired
    {
        if (! is_null($allowedEmptyTypes) && ! is_array($allowedEmptyTypes)) {
            throw new \InvalidArgumentException('$allowedEmptyTypes must be an array or null');
        }

        if (is_null($allowedEmptyTypes)) {
            if ($validator instanceof GetAllowedEmptyTypes) {
                // get the default empty types
                $allowedEmptyTypes = $validator->getAllowedEmptyTypes();
            } else {
                $allowedEmptyTypes = [];
            }
        }

        // add the isRequired validator to the chain
        return new IsRequired($allowedEmptyTypes);
    }

    /**
     * Return IsSame validator
     *
     * @param $match
     * @param bool $strict
     * @return IsSame
     */
    public static function isSame($match, bool $strict = false) : IsSame
    {
        return new IsSame($match, $strict);
    }

    /**
     * Return IsString validator
     *
     * @param bool $strict
     * @return IsString
     */
    public static function isString(bool $strict = false) : IsString
    {
        return new IsString($strict);
    }

    /**
     * Validate the string length of the item
     *
     * @param int $min
     * @param int $max
     * @param null|string $encoding
     * @return StringLength
     */
    public static function stringLength(int $min = 0, int $max = 0, $encoding = null) : StringLength
    {
        return new StringLength($min, $max, $encoding);
    }

    /**
     * Call the helpers
     *
     * @param $name
     * @param $arguments
     * @return false|Chain|Resolver|Validator
     */
    public static function __callStatic($name, $arguments)
    {
        if (! isset(self::$helper[$name])) {
            throw new NonExistentHelperException(
                $name . ' doesn\'t exist. If you\'re trying to use a custom helper you need to call setHelper first.'
            );
        }

        // call the helper
        return static::resolveHelper($name, $arguments);
    }
}
