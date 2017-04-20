<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Resolver\ToValue;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsArrayOfType;
use Mizmoz\Validate\Validator\IsNumeric;
use Mizmoz\Validate\Validator\IsString;

class IsArrayOfTypeTest extends ValidatorTestCaseAbstract
{
    public function testIsArrayOfType()
    {
        // valid item
        $this->assertTrue((new IsArrayOfType([new IsString]))->validate(['a', 'b', 'c'])->isValid());
        $this->assertTrue((new IsArrayOfType([new IsNumeric]))->validate(['1', 2, 3.0])->isValid());

        // invalid item
        $this->assertFalse((new IsArrayOfType([new IsString]))->validate([false, []])->isValid());
        $this->assertFalse((new IsArrayOfType([new IsString]))->validate(null)->isValid());
        $this->assertFalse((new IsArrayOfType([new IsString]))->validate('hello')->isValid());
    }

    public function testIsArrayOfTypeNested()
    {
        // slightly more complicated nested item
        $validate = (new IsArrayOfType(
            (new IsArrayOfType(
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
        $validate = Validate::isArrayOfType(
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
     * Test the items in the array are resolved
     */
    public function testResolveChild()
    {
        $validate = Validate::isArrayOfType(
            Validate::isInteger()
                ->resolveTo(new ToValue(function ($value) {
                    return $value + 100;
                }))
        );

        $data = [
            1, 3, 5
        ];

        $dataResolve = [
            101, 103, 105
        ];

        \Mizmoz\Validate\Tests\Helper\Printer::stopNext();

        $result = $validate->validate($data);
        $this->assertTrue($result->isValid());
        $this->assertEquals($dataResolve, $result->getValue());
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
        $validate = Validate::isArrayOfType(Validate::isString());

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
        $this->assertEquals('[{"isString":{"strict":false}}]', json_encode(new IsArrayOfType(Validate::isString())));
        $this->assertEquals('[{"isInteger":{"strict":false}}]', json_encode(new IsArrayOfType(Validate::isInteger())));
    }
}
