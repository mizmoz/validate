<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsShape;
use Mizmoz\Validate\Validator\IsString;

class IsShapeTest extends TestCase
{
    public function testIsShapeOfStdObject()
    {
        $data = (object)[
            'name' => 'Dave',
            'age' => 55,
        ];

        $result = (new IsShape([
            'name' => Validate::isString()->isRequired(),
            'age' => Validate::isInteger()->isRequired()
        ]))->validate($data);

        // should be valid
        $this->assertTrue($result->isValid());
    }

    public function testIsShapeOfStdArray()
    {
        $data = [
            'name' => 'Bob',
            'age' => 55,
        ];

        $result = (new IsShape([
            'name' => Validate::isString()->isRequired(),
            'age' => Validate::isInteger()->isRequired()
        ]))->validate($data);

        // should be valid
        $this->assertTrue($result->isValid());
    }

    /**
     * Test we don't break when passing a ValueWasNotSet object
     */
    public function testValueWasNotSet()
    {
        $validate = new IsShape([
            'name' => new IsString()
        ]);

        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());
    }

    public function testUsingDefaultValueWithNestedShapes()
    {
        $validate = new IsShape([
            'games' => new IsShape([
                'title' => Validate::isString()
                    ->setDefault('Monopoly')
            ])
        ]);

        // do this so we can check using arrays as the value is actually an ArrayAccess object
        $result = $validate->validate([])->getValue();

        // check for the games key
        $this->assertArrayHasKey('games', $result);
        $this->assertArrayHasKey('title', $result['games']);
        $this->assertEquals('Monopoly', $result['games']['title']);

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


    public function testMoreLotsOfNesting()
    {
        $validate = Validate::set([
            'name' => Validate::isString(),
            'segment' => Validate::isArrayOfType(
                Validate::isShape([
                    'name' => Validate::isString(),
                    'params' => Validate::isString(),
                    'match' => Validate::isOneOf(['all', 'any'])
                        ->setDefault('all'),
                ])
            )
        ]);

        $data = [
            [
                'name' => 'Ian',
                'segment' => [
                    'name' => 123
                ],
            ]
        ];

        $result = $validate->validate($data);
        $this->assertTrue($result->isValid());
        $this->assertEquals($data, $result->getValue());
    }

    public function testShapeDescription()
    {
        $validator = Validate::set([
            'likes' => Validate::isShape([
                'music' => Validate::isShape([
                    'bands' => Validate::isString(),
                ]),
            ])
                ->setDescription('Name of the segment')
                ->isRequired(),
            'status' => Validate::isOneOf(['active', 'inactive'])
                ->setDefault('active')
        ]);

        $this->assertEquals([
            'likes' => [
                'description' => 'Name of the segment',
                'isRequired' => true,
                'isShape' => [
                    'music' => [
                        'isShape' => [
                            'bands' => [
                                'isString' => [
                                    'strict' => false,
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'status' => [
                'isOneOf' => [
                    'allowed' => [
                        'active',
                        'inactive'
                    ]
                ],
                'toDefaultValue' => 'active'
            ]

        ], $validator->getDescription());
    }
}
