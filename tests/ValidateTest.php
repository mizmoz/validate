<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests;

use Mizmoz\Validate\Resolver\ToClass;
use Mizmoz\Validate\Validate;

class ValidateTest extends TestCase
{
    public function testSimpleValidation()
    {
        $this->assertTrue(Validate::isNumeric()->validate(123)->isValid());
        $this->assertTrue(Validate::isNumeric()->validate('123')->isValid());

        $this->assertTrue(Validate::isNumeric()->isRequired()->validate(0)->isValid());
        $this->assertTrue(Validate::isNumeric()->isRequired()->validate(123)->isValid());
        $this->assertTrue(Validate::isNumeric()->isRequired()->validate('123')->isValid());

        $this->assertFalse(Validate::isNumeric()->isRequired()->validate('')->isValid());
        $this->assertFalse(Validate::isNumeric()->isRequired()->validate(false)->isValid());
        $this->assertFalse(Validate::isNumeric()->isRequired()->validate(null)->isValid());
    }

    public function testResolveToObject()
    {
        $this->assertEquals(
            (object)['name' => 'Ian'],
            Validate::isArray()->resolveTo(new ToClass(\stdClass::class))->validate(['name' => 'Ian'])->getValue()
        );

        $this->assertEquals(
            (new \DateTime('2016-01-01')),
            Validate::isArray()
                ->resolveTo(new ToClass(\DateTime::class, ToClass::VALUE_IS_PROPERTY_LIST))
                ->validate(['2016-01-01'])
                ->getValue()
        );
    }
}
