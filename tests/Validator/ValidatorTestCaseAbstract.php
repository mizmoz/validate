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

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;

abstract class ValidatorTestCaseAbstract extends TestCase
{
    /**
     * Test using the Description helper to describe the validation
     */
    abstract public function testDescription();

    /**
     * Test the required states work
     */
    abstract public function testIsRequired();

    /**
     * Check the model serialises
     *
     */
    abstract public function testJsonSerialize();
}
