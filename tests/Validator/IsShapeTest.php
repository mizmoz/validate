<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\IsShape;

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

    public function testUsingDefaultValueWithNestedShapes()
    {
        $validate = new IsShape([
            'games' => new IsShape([
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
}
