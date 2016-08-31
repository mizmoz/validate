<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsArray;

class IsArrayTest extends TestCase
{
    public function testIsString()
    {
        $validator = new IsArray();

        // valid string
        $this->assertTrue($validator->validate([])->isValid());
        $this->assertTrue($validator->validate([1, 2, 3])->isValid());

        // invalid string
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(new \stdClass())->isValid());

        // allow objects that have toArray
        $this->assertTrue($validator->validate(new class {
            public function toArray() : array
            {
                return [];
            }
        })->isValid());

        // strict validation of objects that have toArray
        $this->assertFalse((new IsArray(true))->validate(new class {
            public function toArray() : array
            {
                return [];
            }
        })->isValid());

        // not toArray method
        $this->assertFalse($validator->validate(new class {
            // empty
        })->isValid());
    }
}
