<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsFilter;

class IsFilterTest extends TestCase
{
    public function testIsOneOf()
    {
        $this->markTestIncomplete();

        $validator = new IsFilter([
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
        ]);

        // valid item
        $this->assertTrue($validator->validate('#paying')->isValid());

//        // validate and apply the filters to the string
//        $decorator = $validator->validate('#paying')->getValue();
//        $this->assertEquals('#paying=hello', $decorator('hello'));
//
//        // validate and apply the filters to the string
//        $decorator = $validator->validate('#active')->getValue();
//        $this->assertEquals('#active=hello', $decorator('networkStatus'));
    }
}
