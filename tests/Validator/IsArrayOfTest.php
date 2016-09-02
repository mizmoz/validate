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
use Mizmoz\Validate\Validator\IsInteger;
use Mizmoz\Validate\Validator\IsNumeric;
use Mizmoz\Validate\Validator\IsString;

class IsArrayOfTest extends ValidatorTestCaseAbstract
{
    public function testIsArrayOf()
    {
        // valid item
        $this->assertTrue((new IsArrayOf(new IsString))->validate(['a', 'b', 'c'])->isValid());
        $this->assertTrue((new IsArrayOf(new IsNumeric))->validate(['1', 2, 3.0])->isValid());

        // invalid item
        $this->assertFalse((new IsArrayOf(new IsString))->validate([false, []])->isValid());
        $this->assertFalse((new IsArrayOf(new IsString))->validate(null)->isValid());
        $this->assertFalse((new IsArrayOf(new IsString))->validate('hello')->isValid());
    }

    public function testIsArrayOfNested()
    {
        // slightly more complicated nested item
        $validate = (new IsArrayOf(
            (new IsArrayOf(
                new IsString()
            ))
        ));

        // valid arrays of arrays with strings in
        $this->assertTrue($validate->validate([
            ['a', 'b', 'c'],
            ['e', 'f', 'g']
        ])->isValid());

        // this is fine as long as any of the children aren't required
        $this->assertTrue($validate->validate([])->isValid());

        // not valid
        $this->assertFalse($validate->validate([
            ['1', 2, 3.0],
            ['e', 'f', 'g']
        ])->isValid());

        $this->assertFalse($validate->validate(null)->isValid());
    }

    public function testLotsOfNesting()
    {
        $validate = Validate::isArrayOf(
            Validate::isShape([
                'name' => Validate::isString(),
                'match' => Validate::isOneOf(['all', 'any'])
                    ->setDefault('all'),
            ])
        );

        $data = [
            [
                'name' => 'Ian',
                'match' => 'all',
            ]
        ];

        $result = $validate->validate($data);
        $this->assertTrue($result->isValid());
        $this->assertEquals($data, $result->getValue());
    }

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        $validate = Validate::isArrayOf(Validate::isString());

        $this->assertTrue($validate->validate([
            'name' => 'Ian'
        ])->isValid());

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
        $this->assertEquals('{"isString":{"strict":false}}', json_encode(new IsArrayOf(new IsString())));
        $this->assertEquals('{"isInteger":{"strict":false}}', json_encode(new IsArrayOf(new IsInteger())));
    }
}
