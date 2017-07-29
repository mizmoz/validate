<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsDecimal;

class IsDecimalTest extends ValidatorTestCaseAbstract
{
    public function testIsDecimal()
    {
        $validator = new IsDecimal(2);

        // valid decimals
        $this->assertTrue($validator->validate('123.50')->isValid());
        $this->assertTrue($validator->validate('0.00')->isValid());
        $this->assertTrue($validator->validate('1.23')->isValid());
        $this->assertTrue($validator->validate(-123.43)->isValid());

        // invalid integers
        $this->assertFalse($validator->validate(0)->isValid());
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate('123')->isValid());
        $this->assertFalse($validator->validate('123.453')->isValid());
        $this->assertFalse($validator->validate(123.5)->isValid());
        $this->assertFalse($validator->validate('string')->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    public function testGetAllowedTypes()
    {
        // none strict validation
        $this->assertEquals(['0.00'], (new IsDecimal(2))->getAllowedEmptyTypes());
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
        $this->assertTrue(Validate::isDecimal(2)->validate('1.00')->isValid());
        $this->assertTrue(Validate::isDecimal()->validate(new ValueWasNotSet())->isValid());
        $this->assertFalse(Validate::isDecimal()->isRequired()->validate('')->isValid());
    }

    /**
     * Test serialisation
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"decimalPlaces":2}', json_encode(new IsDecimal(2)));
        $this->assertEquals('{"decimalPlaces":4}', json_encode(new IsDecimal(4)));
    }
}
