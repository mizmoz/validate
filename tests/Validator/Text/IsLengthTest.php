<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator\Text;

use Mizmoz\Validate\Exception\ValidationException;
use Mizmoz\Validate\Tests\Validator\ValidatorTestCaseAbstract;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\Text\IsLength;

class IsLengthTest extends ValidatorTestCaseAbstract
{
    /**
     * Test the text length validator works with all types of encoding
     */
    public function testTextIsLength()
    {
        $this->assertTrue(Validate::isString()->textIsLength(0, 2)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->textIsLength(2, 2)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->textIsLength(0, 100)->validate('Hi')->isValid());
        $this->assertTrue(Validate::isString()->textIsLength(0, 2)->validate('東京')->isValid());
        $this->assertTrue(Validate::isString()->textIsLength(2, 2)->validate('東京')->isValid());
        $this->assertTrue(Validate::isString()->textIsLength(0, 100)->validate('東京')->isValid());

        // add required to the chain
        $this->assertTrue(
            Validate::isString()
                ->textIsLength(0, 100)
                ->isRequired()
                ->validate('東京')
                ->isValid()
        );

        // no required item passed
        $this->assertFalse(
            Validate::isString()
                ->textIsLength(0, 100)
                ->isRequired()
                ->validate('')
                ->isValid()
        );

        // not long enough
        $this->assertFalse(
            Validate::isString()
                ->textIsLength(10, 100)
                ->isRequired()
                ->validate('東京')
                ->isValid()
        );
    }

    public function testValueWasNotSet()
    {
        $this->assertTrue((new IsLength(0, 100))->validate(new ValueWasNotSet())->isValid());
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
        $this->assertEquals('{"min":0,"max":100}', json_encode(new IsLength(0, 100)));
        $this->assertEquals('{"min":10,"max":50}', json_encode(new IsLength(10, 50)));
    }
}
