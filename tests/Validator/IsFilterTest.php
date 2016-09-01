<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsFilter;

class IsFilterTest extends ValidatorTestCaseAbstract
{
    public function testIsOneOf()
    {
        $validator = new IsFilter([
            // apply the where using a callback
            '#paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // apply the where using a callback
            '#nearly-paying' => function ($hashTag, $value) {
                return $hashTag . '<>' . $value;
            },

            // use multiple hash tags and use the hash tag value as the where clause value
            '#active|#credit-hold' => 'networkStatus',
        ]);

        // valid item
        $this->assertTrue($validator->validate('#paying')->isValid());

        // check we get the callback from above in an array
        $decorator = $validator->validate('#paying')->getValue();
        $this->assertEquals(function ($hashTag, $value) {
            return $hashTag . '=' . $value;
        }, $decorator['#paying']);

        // callback works as expected?
        $this->assertEquals('#paying=cheese', $decorator['#paying']('#paying', 'cheese'));
    }

    /**
     * Test the is required is behaving correctly.
     */
    public function testIsRequired()
    {
        $validate = Validate::isFilter(['#cheese', '#yolo']);

        $this->assertTrue($validate->validate('#cheese')->isValid());
        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());

        // make required to check failure
        $validate->isRequired();
        $this->assertFalse($validate->validate('')->isValid());
    }

    /**
     * Test serialisation
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"allowed":["#cheese","#yolo"]}', json_encode(new IsFilter([
            '#cheese', '#yolo'
        ])));

        $this->assertEquals('{"allowed":["#paying","#nearly-paying","#active","#credit-hold"]}', json_encode(new IsFilter([
            // apply the where using a callback
            '#paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // apply the where using a callback
            '#nearly-paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // use multiple hash tags and use the hash tag value as the where clause value
            '#active|#credit-hold' => 'networkStatus',
        ])));
    }
}
