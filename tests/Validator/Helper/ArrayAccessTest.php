<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
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
