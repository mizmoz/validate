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

namespace Mizmoz\Validate\Tests\Helper;

trait HasMockeryTrait
{
    /**
     * Set strict errors off and return the mock object
     *
     * @param $argument
     * @return \Mockery\MockInterface
     */
    public function mock($argument)
    {
        if (defined('E_STRICT')) {
            error_reporting('E_ALL ^ E_STRICT');
        }

        return \Mockery::mock($argument);
    }
}
