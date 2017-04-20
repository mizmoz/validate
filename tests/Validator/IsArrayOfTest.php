<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsArrayOf;

class IsArrayOfTest extends ValidatorTestCaseAbstract
{
    public function testIsArrayOf()
    {
        // valid item
        $this->assertTrue((new IsArrayOf(['a', 'b', 'c', 'd']))->validate(['a', 'b', 'c'])->isValid());
        $this->assertTrue((new IsArrayOf(['1', '2', 3, 4]))->validate(['2', 3])->isValid());

        // invalid item
        $this->assertFalse((new IsArrayOf(['a']))->validate(['b'])->isValid());
        $this->assertFalse((new IsArrayOf([]))->validate(null)->isValid());
        $this->assertFalse((new IsArrayOf([]))->validate('hello')->isValid());
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
        $validate = Validate::isArrayOf(['a', 'b']);

        $this->assertTrue($validate->validate([])->isValid());
        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());

        // now set to required
        $validate->isRequired();

        $this->assertFalse($validate->validate(null)->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"allowed":["a","b"]}', json_encode(new IsArrayOf(['a', 'b'])));
        $this->assertEquals('{"allowed":[1,2,3,4]}', json_encode(new IsArrayOf([1, 2, 3, 4])));
    }
}
