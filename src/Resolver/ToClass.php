<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Exception\RuntimeException;

class ToClass implements Resolver
{
    /**
     * @var mixed
     */
    private $class;

    /**
     * @var string
     */
    private $valuePropertyType;

    /**
     * ToClass constructor.
     * @param string $class Class to resolve the value to
     * @param string $valuePropertyType
     */
    public function __construct(string $class, string $valuePropertyType = self::VALUE_IS_PROPERTY)
    {
        $this->class = $class;
        $this->valuePropertyType = $valuePropertyType;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        switch ($this->class) {
            case 'stdClass':
                return (new ToStdClass())->resolve($value);
        }

        // attempt to resolve the class using reflection
        $reflectionClass = new \ReflectionClass($this->class);

        switch ($this->valuePropertyType) {
            case self::VALUE_IS_PROPERTY:
                return $reflectionClass->newInstance($value);
            case self::VALUE_IS_PROPERTY_LIST:
                return $reflectionClass->newInstanceArgs($value);
            case self::VALUE_IS_NAMED_PROPERTY_LIST:
                throw new RuntimeException('Not implemented yet, sorry!');
        }
    }
}
