<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;

class StringLengthTest extends TestCase
{
    /**
     * Test the string length validator works with all types of encoding
     */
    public function testStringLength()
    {
        $this->assertTrue(Validate::isString()->stringLength(0, 2)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->stringLength(2, 2)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->stringLength(0, 100)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->stringLength(0, 2)->validate('東京')->isValid());
        $this->assertTrue(Validate::isString()->stringLength(2, 2)->validate('東京')->isValid());
        $this->assertTrue(Validate::isString()->stringLength(0, 100)->validate('東京')->isValid());

        // add required to the chain
        $this->assertTrue(
            Validate::isString()
                ->stringLength(0, 100)
                ->isRequired()
                ->validate('東京')
                ->isValid()
        );

        // no required item passed
        $this->assertFalse(
            Validate::isString()
                ->stringLength(0, 100)
                ->isRequired()
                ->validate('')
                ->isValid()
        );

        // not long enough
        $this->assertFalse(
            Validate::isString()
                ->stringLength(10, 100)
                ->isRequired()
                ->validate('東京')
                ->isValid()
        );
    }
}
