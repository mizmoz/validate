<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\IsArrayOfShape;

class IsArrayOfShapeTest extends TestCase
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

    public function testGetSimpleDescription()
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
    public function testGetDescription()
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

    public function testGetDescriptionWithLotsOfNesting()
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
            'games' => Validate::set([
                'title' => Validate::isString()->setDefault('Monopoly')
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
}
