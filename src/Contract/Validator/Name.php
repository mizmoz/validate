<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Contract\Validator;

/**
 * Get the validator name
 *
 * @package Mizmoz\Validate\Contract\Validator
 */
interface Name
{
    /**
     * Get the validator name, should be lower case first camel cased like isArrayOf
     *
     * @return string
     */
    public function getName(): string;
}
