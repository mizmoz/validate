<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsArray;

class IsArrayTest extends ValidatorTestCaseAbstract
{
    public function testIsString()
    {
        $validator = new IsArray();

        // valid string
        $this->assertTrue($validator->validate([])->isValid());
        $this->assertTrue($validator->validate([1, 2, 3])->isValid());

        // invalid string
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(false)->isValid());
        $this->assertFalse($validator->validate(new \stdClass())->isValid());

        // allow objects that have toArray
        $this->assertTrue($validator->validate(new class {
            public function toArray() : array
            {
                return [];
            }
        })->isValid());

        // strict validation of objects that have toArray
        $this->assertFalse((new IsArray(true))->validate(new class {
            public function toArray() : array
            {
                return [];
            }
        })->isValid());

        // not toArray method
        $this->assertFalse($validator->validate(new class {
            // empty
        })->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        // Basic validation
        $description = Description::getDescription(new IsArray());

        $this->assertEquals([
            'isArray' => 'isArray',
        ], $description);
    }

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        $this->assertTrue(Validate::isArray()->isRequired()->validate([1])->isValid());
        $this->assertFalse(Validate::isArray()->isRequired()->validate([])->isValid());
        $this->assertFalse(Validate::isArray()->isRequired()->validate()->isValid());
        $this->assertFalse(Validate::isArray()->isRequired()->validate(new ValueWasNotSet())->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{}', json_encode(new IsArray()));
    }
}
