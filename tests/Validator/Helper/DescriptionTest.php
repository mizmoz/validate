<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
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
