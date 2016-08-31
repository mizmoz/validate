<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsNumeric;

class IsNumericTest extends TestCase
{
    public function testIsNumeric()
    {
        $validator = new IsNumeric();

        // valid numbers
        $this->assertTrue($validator->validate(0)->isValid());
        $this->assertTrue($validator->validate(123)->isValid());
        $this->assertTrue($validator->validate('123')->isValid());
        $this->assertTrue($validator->validate(123.5)->isValid());
        $this->assertTrue($validator->validate('123.5')->isValid());
        $this->assertTrue($validator->validate(-123.43)->isValid());
        $this->assertTrue($validator->validate(new ValueWasNotSet)->isValid());

        // invalid numbers
        $this->assertFalse($validator->validate('')->isValid());
        $this->assertFalse($validator->validate('string')->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
    }

    public function testGetErrorMessage()
    {
        $validator = new IsNumeric();

        // valid, no error message
        $this->assertSame([], $validator->validate(0)->getMessages());

        // invalid
        $this->assertSame(['isNumeric' => 'Value is not a valid number'], $validator->validate('')->getMessages());
    }
}
