<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver\Helper;

trait CreateObjectTrait
{
    /**
     * @var array
     */
    private static $createObjectReturnValues = [];

    /**
     * Create the object
     *
     * @param string $className
     * @param array $arguments
     * @return object
     */
    private static function createObject(string $className, array $arguments = [])
    {
        if (self::$createObjectReturnValues) {
            // attempt to resolve from the container
            $key = $className . '::' . json_encode($arguments);

            if (array_key_exists($key, self::$createObjectReturnValues)) {
                return self::$createObjectReturnValues[$key];
            } else if (array_key_exists($className, self::$createObjectReturnValues)) {
                return self::$createObjectReturnValues[$className];
            }
        }

        if (! $arguments) {
            // no arguments, just instantiate the class
            return new $className;
        }

        // use reflection to instantiate the class
        return (new \ReflectionClass($className))->newInstanceArgs($arguments);
    }

    /**
     * Set the return value for the create object method
     *
     * @param mixed $returnValue
     * @param string $className
     * @param array $arguments
     * @param bool $anyArguments
     */
    public static function setCreateObjectReturnValue(
        $returnValue,
        string $className,
        array $arguments = [],
        $anyArguments = false
    ) {
        $key = ($anyArguments ? $className : $className . '::' . json_encode($arguments));
        self::$createObjectReturnValues[$key] = $returnValue;
    }
}
