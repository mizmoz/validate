<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\IsOneOfType;

class IsOneOfTypeTest extends ValidatorTestCaseAbstract
{
    public function testIsOneOfType()
    {
        $validator = new IsOneOfType([
            Validate::isInteger(),
            Validate::isOneOf(['me'])
        ]);

        // valid item
        $this->assertTrue($validator->validate(123)->isValid());
        $this->assertTrue($validator->validate('me')->isValid());
        $this->assertTrue($validator->validate('1')->isValid());

        // invalid item
        $this->assertFalse($validator->validate('cheese')->isValid());
        $this->assertFalse($validator->validate([])->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        // Basic validation
        $description = Description::getDescription(new IsOneOfType([
            Validate::isInteger(),
            Validate::isString(),
        ]));

        $this->assertEquals([
            'isOneOfType' => [
                [
                    'isInteger' => [
                        'strict' => false,
                    ],
                ],
                [

                    'isString' => [
                        'strict' => false,
                    ],
                ]
            ],
        ], $description);
    }

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        // TODO: Implement testIsRequired() method.
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        // TODO: Implement testJsonSerialize() method.
    }
}
