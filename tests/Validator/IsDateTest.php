<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsDate;

class IsDateTest extends TestCase
{
    public function testIsDate()
    {
        $validator = new IsDate();

        // valid item
        $this->assertTrue($validator->validate('2012-01-01')->isValid());
        $this->assertTrue($validator->validate('1999-12-31')->isValid());
        $this->assertTrue($validator->validate('1999-12-01')->isValid());
        $this->assertTrue($validator->validate(new ValueWasNotSet())->isValid());

        // valid with different date formats
        $this->assertTrue((new IsDate('d/m/Y'))->validate('01/01/2016')->isValid());

        // no leading zeros
        $this->assertTrue((new IsDate('n/j/y'))->validate('12/31/16')->isValid());

        // invalid item
        $this->assertFalse((new IsDate('d/m/Y'))->validate('1/1/2016')->isValid());
        $this->assertFalse($validator->validate('1999-03-1')->isValid());
        $this->assertFalse($validator->validate('1999-3-9')->isValid());

        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    public function testDateResolutionToDateTime()
    {
        // check we resolve to a DateTime object
        $dateTime = (new IsDate())->validate('2012-01-01')->getValue();
        $this->assertInstanceOf(\DateTime::class, $dateTime);

        // check the dates are the same
        $this->assertEquals(
            (new \DateTime('2012-01-01', new \DateTimeZone('UTC')))->format('Y-m-d'),
            $dateTime->format('Y-m-d')
        );

        // check not resolving the object
        $dateTime = (new IsDate('Y-m-d', false))->validate('2012-01-01')->getValue();
        $this->assertEquals('2012-01-01', $dateTime);

        // dont resolve when the value is invalid
        $dateTime = (new IsDate())->validate('rar')->getValue();
        $this->assertEquals('rar', $dateTime);
    }
}
