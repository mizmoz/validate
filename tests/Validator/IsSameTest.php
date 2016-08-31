<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsSame;

class IsSameTest extends TestCase
{
    public function testIsSame()
    {
        // valid item
        $this->assertTrue((new IsSame('hello'))->validate('hello')->isValid());
        $this->assertTrue((new IsSame('world'))->validate('world')->isValid());
        $this->assertTrue((new IsSame(123))->validate('123')->isValid());
        $this->assertTrue((new IsSame(''))->validate(false)->isValid());

        // invalid item
        $this->assertFalse((new IsSame('hello'))->validate('world')->isValid());
        $this->assertFalse((new IsSame('hello', true))->validate('world')->isValid());
        $this->assertFalse((new IsSame(123, true))->validate('123')->isValid());
        $this->assertFalse((new IsSame(31, true))->validate(null)->isValid());
        $this->assertFalse((new IsSame(false, true))->validate(null)->isValid());
    }
}
