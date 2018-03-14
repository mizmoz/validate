<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;

class ToValue implements Resolver
{
    /**
     * @var mixed
     */
    private $newValue;

    /**
     * ToClass constructor.
     * @param $newValue
     */
    public function __construct($newValue)
    {
        $this->newValue = $newValue;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        $newValue = $this->newValue;
        return (is_callable($newValue) ? $newValue($value) : $newValue);
    }
}
