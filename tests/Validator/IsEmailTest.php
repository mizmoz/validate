<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsEmail;

class IsEmailTest extends TestCase
{
    public function testIsEmail()
    {
        // valid item
        $this->assertTrue((new IsEmail())->validate('support@mizmoz.com')->isValid());
        $this->assertTrue((new IsEmail())->validate('super.support@mizmoz.com')->isValid());
        $this->assertTrue((new IsEmail())->validate('super.support@mizmoz.co.uk')->isValid());
        $this->assertTrue((new IsEmail())->validate(new ValueWasNotSet())->isValid());

        // invalid
        $this->assertFalse((new IsEmail())->validate('@mizmoz.com')->isValid());
        $this->assertFalse((new IsEmail())->validate('as..@mizmoz.com')->isValid());
        $this->assertFalse((new IsEmail())->validate('as..')->isValid());
        $this->assertFalse((new IsEmail())->validate('')->isValid());
        $this->assertFalse((new IsEmail())->validate()->isValid());

        // check disposable emails are not allowed
        $this->assertFalse((new IsEmail(['allowDisposable' => false]))->validate('bob@discardmail.com')->isValid());
    }
}
