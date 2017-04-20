<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator\Text;

use Mizmoz\Validate\Tests\Validator\ValidatorTestCaseAbstract;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Number\IsRange;

class IsRangeTest extends ValidatorTestCaseAbstract
{
    /**
     * Test the number range
     */
    public function testNumberIsRange()
    {
        $this->assertTrue(Validate::isNumeric()->numberIsRange(0, 2)->validate(0)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(0, 2)->validate(1)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(0, 2)->validate(2)->isValid());

        // negative numbers
        $this->assertTrue(Validate::isNumeric()->numberIsRange(-127, 127)->validate(-127)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(-127, 127)->validate(0)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(-127, 127)->validate(127)->isValid());

        // fails
        $this->assertFalse(Validate::isNumeric()->numberIsRange(0, 2)->validate(3)->isValid());
        $this->assertFalse(Validate::isNumeric()->numberIsRange(0, 2)->validate(100)->isValid());
        $this->assertFalse(Validate::isNumeric()->numberIsRange(0, 2)->validate(-20)->isValid());
        $this->assertFalse(Validate::isNumeric()->numberIsRange(-10, 0)->validate(10)->isValid());

        // add required to the chain
        $this->assertTrue(
            Validate::isNumeric()
                ->numberIsRange(0, 100)
                ->isRequired()
                ->validate(50)
                ->isValid()
        );

        // no required item passed
        $this->assertFalse(
            Validate::isNumeric()
                ->numberIsRange(0, 100)
                ->isRequired()
                ->validate('')
                ->isValid()
        );

        // not long enough
        $this->assertFalse(
            Validate::isInteger()
                ->textIsLength(10, 100)
                ->isRequired()
                ->validate(5)
                ->isValid()
        );
    }

    public function testNumberIsGreater()
    {
        $this->assertTrue(Validate::isNumeric()->numberIsRange(10)->validate(10)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(10)->validate(100)->isValid());
        $this->assertFalse(Validate::isNumeric()->numberIsRange(10)->validate(0)->isValid());
    }

    public function testNumberIsLessThan()
    {
        $this->assertTrue(Validate::isNumeric()->numberIsRange(null, 10)->validate(10)->isValid());
        $this->assertTrue(Validate::isNumeric()->numberIsRange(null, 10)->validate(-1)->isValid());
        $this->assertFalse(Validate::isNumeric()->numberIsRange(null, 10)->validate(100)->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        $this->markTestSkipped('Need to implement test for ' . __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        // doesn't make sense for this validator
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"min":0,"max":100}', json_encode(new IsRange(0, 100)));
        $this->assertEquals('{"min":10,"max":50}', json_encode(new IsRange(10, 50)));
    }
}
