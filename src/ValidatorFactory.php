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
use Mizmoz\Validate\Validator\IsRequired;

class ValidatorFactory
{
    /**
     * Validator namespace
     */
    const VALIDATOR_NAMESPACE = __NAMESPACE__ . '\\Validator\\';

    /**
     * Number validation
     */
    const VALIDATOR_NUMBER_NAMESPACE = __NAMESPACE__ . '\\Validator\\Number\\';

    /**
     * Text validation
     */
    const VALIDATOR_TEXT_NAMESPACE = __NAMESPACE__ . '\\Validator\\Text\\';

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
        'isArray' => self::VALIDATOR_NAMESPACE . 'IsArray',
        'isArrayOf' => self::VALIDATOR_NAMESPACE . 'IsArrayOf',
        'isArrayOfShape' => self::VALIDATOR_NAMESPACE . 'IsArrayOfShape',
        'isArrayOfType' => self::VALIDATOR_NAMESPACE . 'IsArrayOfType',
        'isBoolean' => self::VALIDATOR_NAMESPACE . 'IsBoolean',
        'isDate' => self::VALIDATOR_NAMESPACE . 'IsDate',
        'isDecimal' => self::VALIDATOR_NAMESPACE . 'IsDecimal',
        'isEmail' => self::VALIDATOR_NAMESPACE . 'IsEmail',
        'isEmailDisposable' => self::VALIDATOR_NAMESPACE . 'IsEmailDisposable',
        'isFilter' => self::VALIDATOR_NAMESPACE . 'IsFilter',
        'isInteger' => self::VALIDATOR_NAMESPACE . 'IsInteger',
        'isNumeric' => self::VALIDATOR_NAMESPACE . 'IsNumeric',
        'isObject' => self::VALIDATOR_NAMESPACE . 'IsObject',
        'isOneOf' => self::VALIDATOR_NAMESPACE . 'IsOneOf',
        'isOneOfType' => self::VALIDATOR_NAMESPACE . 'IsOneOfType',
        'isSame' => self::VALIDATOR_NAMESPACE . 'IsSame',
        'isShape' => self::VALIDATOR_NAMESPACE . 'IsShape',
        'isString' => self::VALIDATOR_NAMESPACE . 'IsString',

        // special number validators
        'numberIsRange' => self::VALIDATOR_NUMBER_NAMESPACE . 'IsRange',

        // special text validators
        'textIsLength' => self::VALIDATOR_TEXT_NAMESPACE . 'IsLength',

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

        static::setHelper($name, $callback, $mustBeUnique);
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
