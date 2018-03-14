<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsBoolean;

class IsBooleanTest extends ValidatorTestCaseAbstract
{
    public function testIsBoolean()
    {
        $validator = new IsBoolean;

        // valid item
        $this->assertTrue($validator->validate(1)->isValid());
        $this->assertTrue($validator->validate(0)->isValid());
        $this->assertTrue($validator->validate(true)->isValid());
        $this->assertTrue($validator->validate(false)->isValid());
        $this->assertTrue($validator->validate('1')->isValid());
        $this->assertTrue($validator->validate('0')->isValid());
        $this->assertTrue($validator->validate('true')->isValid());
        $this->assertTrue($validator->validate('false')->isValid());

        // invalid item
        $this->assertFalse($validator->validate('cheese')->isValid());
        $this->assertFalse($validator->validate(123)->isValid());
        $this->assertFalse($validator->validate(null)->isValid());
        $this->assertFalse($validator->validate('')->isValid());
    }

    /**
     * @inheritdoc
     */
    public function testDescription()
    {
        // Basic validation
        $description = Description::getDescription(new IsBoolean());

        $this->assertEquals([
            'isBoolean' => [
                'allowed' => [0, 1, '0', '1', true, false, 'true', 'false']
            ],
        ], $description);
    }

    /**
     * @inheritDoc
     */
    public function testIsRequired()
    {
        $this->assertTrue(Validate::isBoolean()->isRequired()->validate(true)->isValid());
        $this->assertTrue(Validate::isBoolean()->isRequired()->validate(false)->isValid());
        $this->assertFalse(Validate::isBoolean()->isRequired()->validate(new ValueWasNotSet())->isValid());
        $this->assertFalse(Validate::isBoolean()->isRequired()->validate('')->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(
            '{"allowed":[0,1,"0","1",true,false,"true","false"]}',
            json_encode(new IsBoolean())
        );
    }
}
