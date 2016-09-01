<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsInteger;
use Mizmoz\Validate\Validator\IsOneOf;

class IsOneOfTest extends ValidatorTestCaseAbstract
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

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        $validate = Validate::isOneOf(['on', 'off']);

        $this->assertTrue($validate->validate('on')->isValid());
        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());

        // now set to required
        $validate->isRequired();
        $this->assertFalse($validate->validate('')->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"allowed":["active","inactive"]}', json_encode(new IsOneOf(['active', 'inactive'])));


        // test description
        $this->assertEquals([
            'allowed' => [
                'isInteger' => [
                    'strict' => false,
                ],
                'isOneOf' => [
                    'allowed' => [
                        'on', 'off'
                    ]
                ]
            ]
        ], (new IsOneOf([
            new IsInteger(),
            new IsOneOf(['on', 'off']),
        ]))->getDescription());

        // nested one of... not totally sure this is how we should represent this kind of validation
        $this->assertEquals(
            '{"allowed":{"isInteger":{"strict":false},"isOneOf":{"allowed":["on","off"]}}}',
            json_encode(new IsOneOf([
                new IsInteger(),
                new IsOneOf(['on', 'off']),
            ]))
        );
    }
}
