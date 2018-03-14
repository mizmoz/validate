<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator\Helper;

use Mizmoz\Validate\Contract\Validator\Name;
use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\IsString;

class DescriptionTest extends TestCase
{
    public function testGettingObjectName()
    {
        // basic default resolution using the class name
        $this->assertEquals('isString', Description::getName(new IsString()));

        // resolve using the getName method
        $class = new class implements Name {
            /**
             * @inheritDoc
             */
            public function getName(): string
            {
                return 'thisIsATest';
            }
        };

        $this->assertEquals('thisIsATest', Description::getName(new $class()));
    }
}
