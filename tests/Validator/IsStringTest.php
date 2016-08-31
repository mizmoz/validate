<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsString;

class IsStringTest extends TestCase
{
    public function testIsString()
    {
        $validator = new IsString();

        // valid string
        $this->assertTrue($validator->validate('Hello')->isValid());
        $this->assertTrue($validator->validate('123')->isValid());
        $this->assertTrue($validator->validate(new ValueWasNotSet)->isValid());

        // invalid string
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate([])->isValid());
        $this->assertFalse($validator->validate(new \stdClass())->isValid());

        // allow objects that have __toString
        $this->assertTrue($validator->validate(new class {
            public function __toString()
            {
                return 'A String!';
            }
        })->isValid());

        // strict validation of objects that have __toString
        $this->assertFalse((new IsString(true))->validate(new class {
            public function __toString()
            {
                return 'A String!';
            }
        })->isValid());

        // not __toString method
        $this->assertFalse($validator->validate(new class {
            // empty
        })->isValid());
    }

    /**
     * Test the is required is behaving correctly.
     */
    public function testIsRequired()
    {
        $this->assertFalse(Validate::isString()->isRequired()->validate('')->isValid());
    }
}
