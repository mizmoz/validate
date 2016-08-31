<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\IsArrayOf;
use Mizmoz\Validate\Validator\IsNumeric;
use Mizmoz\Validate\Validator\IsString;

class IsArrayOfTest extends TestCase
{
    public function testIsArrayOf()
    {
        // valid item
        $this->assertTrue((new IsArrayOf(new IsString))->validate(['a', 'b', 'c'])->isValid());
        $this->assertTrue((new IsArrayOf(new IsNumeric))->validate(['1', 2, 3.0])->isValid());

        // invalid item
        $this->assertFalse((new IsArrayOf(new IsString))->validate([false, []])->isValid());
        $this->assertFalse((new IsArrayOf(new IsString))->validate(null)->isValid());
        $this->assertFalse((new IsArrayOf(new IsString))->validate('hello')->isValid());
    }

    public function testIsArrayOFNested()
    {
        // slightly more complicated nested item
        $validate = (new IsArrayOf(
            (new IsArrayOf(
                new IsString()
            ))
        ));

        // valid arrays of arrays with strings in
        $this->assertTrue($validate->validate([
            ['a', 'b', 'c'],
            ['e', 'f', 'g']
        ])->isValid());

        // this is fine as long as any of the children aren't required
        $this->assertTrue($validate->validate([])->isValid());

        // not valid
        $this->assertFalse($validate->validate([
            ['1', 2, 3.0],
            ['e', 'f', 'g']
        ])->isValid());

        $this->assertFalse($validate->validate(null)->isValid());
    }
}
