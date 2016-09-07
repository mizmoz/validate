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

namespace Mizmoz\Validate\Tests\Validator\Helper;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ArrayAccess;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class ArrayAccessTest extends TestCase
{
    public function testCreateAccessorFromObject()
    {
        $values = [
            'name' => 'Ian',
            'age' => 123,
        ];

        $access = new ArrayAccess((object)$values);

        // check we can access the object like an array
        $this->assertEquals($values['name'], $access['name']);
        $this->assertEquals($values['age'], $access['age']);

        // check we get a ValueWasNotSet object if we attempt to access something that doesn't exist
        $this->assertInstanceOf(ValueWasNotSet::class, $access['rar']);

        // check we can loop
        foreach ($access as $key => $value) {
            $this->assertEquals($values[$key], $value);
        }
    }
}
