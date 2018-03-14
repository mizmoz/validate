<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsEmailDisposable;

class Disposable extends TestCase
{
    public function testIsEmailDisposable()
    {
        // valid item
        $this->assertTrue((new IsEmailDisposable())->validate('le-phishy@33mail.com')->isValid());
        $this->assertTrue((new IsEmailDisposable())->validate('bob@discardmail.com')->isValid());
        $this->assertTrue((new IsEmailDisposable())->validate('banking-alert@usaanotifications.33mail.com')->isValid());
        $this->assertTrue((new IsEmailDisposable())->validate(new ValueWasNotSet())->isValid());

        // invalid
        $this->assertFalse((new IsEmailDisposable())->validate('support@mizmoz.com')->isValid());
        $this->assertFalse((new IsEmailDisposable())->validate('super.support@mizmoz.com')->isValid());
        $this->assertFalse((new IsEmailDisposable())->validate('super.support@mizmoz.co.uk')->isValid());
    }
}
