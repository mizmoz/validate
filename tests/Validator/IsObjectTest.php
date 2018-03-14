<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsObject;

class IsObjectTest extends TestCase
{
    public function testIsObject()
    {
        $validator = new IsObject();

        // valid string
        $this->assertTrue($validator->validate(new \stdClass())->isValid());

        // invalid string
        $this->assertFalse($validator->validate([])->isValid());
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }
}
