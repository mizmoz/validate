<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\IsOneOf;

class IsOneOfTest extends TestCase
{
    public function testIsOneOf()
    {
        $validator = new IsOneOf(['on', 'off']);

        // valid item
        $this->assertTrue($validator->validate('on')->isValid());
        $this->assertTrue($validator->validate('off')->isValid());

        // invalid item
        $this->assertFalse($validator->validate('cheese')->isValid());
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    public function testIsOnOfNestedValidators()
    {
        // test normal
        $this->assertEquals(
            ['yes', 'no'],
            Validate::isSame('all')->toValue(['yes', 'no'])->validate('all')->getValue()
        );

        // test nesting
        $result = Validate::isOneOf([
            // if we find all return an array of things
            Validate::isSame('all')->toValue(['yes', 'no'])
        ])->setDefault('yes')->validate('all');

        $this->assertTrue($result->isValid());

        $this->assertEquals(['yes', 'no'], $result->getValue());
    }
}
