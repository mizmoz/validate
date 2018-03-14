<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Resolver\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class ToDefaultValue implements Resolver, Description
{
    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private $strict;

    /**
     * ToDefaultValue constructor.
     *
     * @param $defaultValue
     * @param bool $strict
     */
    public function __construct($defaultValue, bool $strict = true)
    {
        $this->defaultValue = $defaultValue;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        if ($value instanceof ValueWasNotSet || (! $this->strict && ! $value)) {
            // value is not set so use the default value
            $defaultValue = $this->defaultValue;

            // check if instance of Closure first as this doesn't fire the autoloader.
            $value = ($defaultValue instanceof \Closure && is_callable($defaultValue)
                ? $defaultValue($value)
                : $defaultValue
            );
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
