<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsArrayOfShape;
use Mizmoz\Validate\Validator\IsString;

class IsArrayOfShapeTest extends ValidatorTestCaseAbstract
{
    public function testIsArrayOfShape()
    {
        $validator = (new IsArrayOfShape([
            'name' => Validate::isString()->isRequired(),
        ]));

        // valid item
        $this->assertTrue($validator->validate(['name' => 'Ian'])->isValid());
        $this->assertTrue($validator->validate(['name' => 'Ian', 'another' => 'value'])->isValid());

        // invalid shape
        $this->assertFalse($validator->validate(['name' => ''])->isValid());
        $this->assertFalse($validator->validate([])->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
    }

    /**
     * Check nesting the array shapes
     */
    public function testIsArrayOfShapeNested()
    {
        // use the Validate::isString helper as we want to make the properties required
        $validator = (new IsArrayOfShape([
            'name' => Validate::isString()->isRequired(),
            'address' => Validate::isArrayOfShape([
                'street' => Validate::isString()
            ])->isRequired()
        ]));

        // valid
        $this->assertTrue($validator->validate([
            'name' => 'Ian',
            'address' => [
                'street' => '101 Street'
            ]
        ])->isValid());

        // missing required fields
        $this->assertFalse($validator->validate([
            'address' => [
                'street' => '101 Street'
            ]
        ])->isValid());

        $this->assertFalse($validator->validate([
            'name' => 'Ian',
            'address' => []
        ])->isValid());

        $this->assertFalse($validator->validate([
            'name' => 'Ian'
        ])->isValid());
    }

    public function xtestGetSimpleDescription()
    {
        $validator = (new IsArrayOfShape([
            'name' => Validate::isString()->isRequired(),
        ]));

        $this->assertEquals([
            'name' => [
                'description' => '',
                'validation' => [
                    'isString',
                    'isRequired',
                ]
            ]
        ], $validator->getDescription());
    }

    /**
     * Test some more complex descriptions
     */
    public function xtestGetDescription()
    {
        $validator = (new IsArrayOfShape([
            'address' => Validate::isArrayOfShape([
                'street' => Validate::isString()->isRequired()
            ]),
            'name' => Validate::isString()->isRequired(),
        ]));

        $this->assertEquals([
            'name' => [
                'description' => '',
                'validation' => [
                    'isString',
                    'isRequired',
                ]
            ],
            'address' => [
                'description' => '',
                'validation' => [],
                'children' => [
                    'street' => [
                        'description' => '',
                        'validation' => [
                            'isString',
                            'isRequired'
                        ]
                    ]
                ]
            ]
        ], $validator->getDescription());
    }

    public function xtestGetDescriptionWithLotsOfNesting()
    {
        $validator = (new IsArrayOfShape([
            'likes' => Validate::isArrayOfShape([
                'music' => Validate::isArrayOfShape([
                    'bands' => Validate::isString(),
                ]),
            ])->isRequired(),
        ]));

        $this->assertEquals([
            'likes' => [
                'description' => '',
                'validation' => [
                    'isRequired',
                ],
                'children' => [
                    'music' => [
                        'description' => '',
                        'validation' => [],
                        'children' => [
                            'bands' => [
                                'description' => '',
                                'validation' => [
                                    'isString'
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ], $validator->getDescription());
    }

    public function testUsingDefaultValue()
    {
        $validate = new IsArrayOfShape([
            'cheese' => Validate::isString()->setDefault('Cheddar')
        ]);

        $this->assertEquals([
            'cheese' => 'Cheddar'
        ], $validate->validate([])->getValue());

        $this->assertEquals([
            'cheese' => 'Edam'
        ], $validate->validate(['cheese' => 'Edam'])->getValue());
    }

    public function testUsingDefaultValueWithNestedShapes()
    {
        $validate = new IsArrayOfShape([
            'games' => new IsArrayOfShape([
                'title' => Validate::isString()
                    ->setDefault('Monopoly')
            ])
        ]);

        $this->assertEquals([
            'games' => [
                'title' => 'Monopoly'
            ]
        ], $validate->validate([])->getValue());

        $this->assertEquals([
            'games' => [
                'title' => 'Chess'
            ]
        ], $validate->validate([
            'games' => [
                'title' => 'Chess'
            ]
        ])->getValue());
    }

    public function testValueWasNotSet()
    {
        $validate = new IsArrayOfShape([
            'name' => new IsString()
        ]);

        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());
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
        $validate = Validate::isArrayOfShape(['name' => new IsString()]);
        $this->assertTrue($validate->validate([
            'name' => 'Ian'
        ])->isValid());

        // true when no values are set
        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());

        // false when required though
        $validate->isRequired();
        $this->assertFalse($validate->validate('')->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"name":{"isString":{"strict":false}}}', json_encode(new IsArrayOfShape([
            'name' => Validate::isString()
        ])));

        $this->assertEquals('{"games":{"isArrayOfShape":{"title":{"isString":{"strict":false},"toDefaultValue":"Monopoly"}}}}', json_encode(new IsArrayOfShape([
            'games' => Validate::isArrayOfShape([
                'title' => Validate::isString()
                    ->setDefault('Monopoly')
            ])
        ])));
    }
}
