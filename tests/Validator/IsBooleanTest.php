<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsBoolean;

class IsBooleanTest extends TestCase
{
    public function testIsBoolean()
    {
        $validator = new IsBoolean;

        // valid item
        $this->assertTrue($validator->validate(1)->isValid());
        $this->assertTrue($validator->validate(0)->isValid());
        $this->assertTrue($validator->validate(true)->isValid());
        $this->assertTrue($validator->validate(false)->isValid());
        $this->assertTrue($validator->validate('1')->isValid());
        $this->assertTrue($validator->validate('0')->isValid());
        $this->assertTrue($validator->validate('true')->isValid());
        $this->assertTrue($validator->validate('false')->isValid());

        // invalid item
        $this->assertFalse($validator->validate('cheese')->isValid());
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }
}
