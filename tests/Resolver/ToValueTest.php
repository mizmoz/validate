<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Resolver;

use Mizmoz\Validate\Resolver\ToValue;
use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Tests\TestModel\User;

class ToValueTest extends TestCase
{
    public function testResolveToModel()
    {
        // set change to a value
        $resolver = new ToValue(123);
        $this->assertEquals(123, $resolver->resolve(1));

        // using a callback
        $resolver = new ToValue(function ($value) {
            // maybe we only want to change the value if it's something in particular
            return ($value === 'me' ? User::current()->userId : $value);
        });
        $this->assertEquals(User::$currentUserId, $resolver->resolve('me'));
        $this->assertEquals(123, $resolver->resolve(123));
    }
}
