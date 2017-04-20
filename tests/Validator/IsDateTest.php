<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Date;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsDate;

class IsDateTest extends ValidatorTestCaseAbstract
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
        $this->assertTrue((new IsDate(['format' => 'd/m/Y']))->validate('01/01/2016')->isValid());

        // no leading zeros
        $this->assertTrue((new IsDate(['format' => 'n/j/y']))->validate('12/31/16')->isValid());

        // allow empty strings with strict off
        $this->assertTrue((new IsDate(['strict' => false]))->validate('')->isValid());

        // invalid item
        $this->assertFalse((new IsDate(['format' => 'd/m/Y']))->validate('1/1/2016')->isValid());
        $this->assertFalse($validator->validate('1999-03-1')->isValid());
        $this->assertFalse($validator->validate('1999-3-9')->isValid());

        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    public function testDateResolutionToDateTime()
    {
        // check we resolve to a Date object
        $dateTime = (new IsDate())->validate('2012-01-01')->getValue();
        $this->assertInstanceOf(Date::class, $dateTime);

        // check the dates are the same and we can use the Date object as a string
        $this->assertEquals(
            (string)new Date('2012-01-01'),
            $dateTime->format('Y-m-d')
        );

        // check not resolving the object
        $dateTime = (new IsDate(['setValueToDateTime' => false]))->validate('2012-01-01')->getValue();
        $this->assertEquals('2012-01-01', $dateTime);

        // dont resolve when the value is invalid
        $dateTime = (new IsDate())->validate('rar')->getValue();
        $this->assertEquals('rar', $dateTime);
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        $this->markTestSkipped('Need to implement test for ' . __METHOD__);
    }

    /**
     * Test the is required is behaving correctly.
     */
    public function testIsRequired()
    {
        $this->assertTrue(Validate::isDate()->validate('2016-01-01')->isValid());
        $this->assertTrue(Validate::isDate()->validate(new ValueWasNotSet())->isValid());
        $this->assertFalse(Validate::isDate()->isRequired()->validate('')->isValid());
    }

    /**
     * Test serialisation
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"format":"Y-m-d"}', json_encode(new IsDate()));
        $this->assertEquals('{"format":"n\/j\/y"}', json_encode(new IsDate(['format' => 'n/j/y'])));
    }
}
