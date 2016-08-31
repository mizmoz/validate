<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
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
