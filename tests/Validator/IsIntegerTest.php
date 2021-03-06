<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Contract\Validator\ToInteger;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsInteger;

class IsIntegerTest extends ValidatorTestCaseAbstract
{
    public function testIsInteger()
    {
        $validator = new IsInteger();
        $validatorStrict = new IsInteger(true);

        // valid integers
        $this->assertTrue($validator->validate(0)->isValid());
        $this->assertTrue($validator->validate(123)->isValid());
        $this->assertTrue($validator->validate('123')->isValid());

        // attempt strict validator
        $this->assertTrue($validatorStrict->validate(123)->isValid());
        $this->assertFalse($validatorStrict->validate('123')->isValid());

        // invalid integers
        $this->assertFalse($validator->validate(123.5)->isValid());
        $this->assertFalse($validator->validate('123.5')->isValid());
        $this->assertFalse($validator->validate(-123.43)->isValid());
        $this->assertFalse($validator->validate('string')->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    public function testIsIntegerWithObjectContract()
    {
        $value = new class implements ToInteger {
            /**
             * @inheritDoc
             */
            public function toInteger(): int
            {
                return 1;
            }
        };

        $validator = new IsInteger();
        $validatorStrict = new IsInteger(true);

        $this->assertTrue($validator->validate($value)->isValid());
        $this->assertTrue($validatorStrict->validate($value)->isValid());
    }

    public function testGetAllowedTypes()
    {
        // none strict validation
        $this->assertEquals([0, '0'], (new IsInteger())->getAllowedEmptyTypes());

        // strict validation
        $this->assertEquals([0], (new IsInteger(true))->getAllowedEmptyTypes());
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
        $this->assertTrue(Validate::isInteger()->validate(1)->isValid());
        $this->assertTrue(Validate::isInteger()->validate(new ValueWasNotSet())->isValid());
        $this->assertFalse(Validate::isInteger()->isRequired()->validate('')->isValid());
    }

    /**
     * Test serialisation
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"strict":false}', json_encode(new IsInteger()));
        $this->assertEquals('{"strict":true}', json_encode(new IsInteger(true)));
    }
}
