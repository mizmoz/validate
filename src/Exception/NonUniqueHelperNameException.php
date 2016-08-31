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

namespace Mizmoz\Validate\Exception;

class NonUniqueHelperNameException extends RuntimeException
{
    /**
     * @var string
     */
    private $helperName;

    /**
     * Get the helper name
     *
     * @return string
     */
    public function getHelperName() : string
    {
        return $this->helperName;
    }

    /**
     * Set the helper name
     *
     * @param string $helperName
     * @return NonUniqueHelperNameException
     */
    public function setHelperName(string $helperName) : NonUniqueHelperNameException
    {
        $this->helperName = $helperName;
        return $this;
    }
}
