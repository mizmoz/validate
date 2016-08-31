<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;

class ToStdClass implements Resolver
{
    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        return (object)$value;
    }
}
