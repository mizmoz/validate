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

namespace Mizmoz\Validate\Contract;

use \Exception;

interface Result
{
    /**
     * Is this result valid?
     *
     * @return bool
     */
    public function isValid() : bool;

    /**
     * Get the messages
     *
     * @return array
     */
    public function getMessages() : array;

    /**
     * Get the name of the result, useful for shapes
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the exception to throw
     *
     * @return Exception
     */
    public function getException() : Exception;

    /**
     * Get the result value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set the allowed types
     *
     * @param array $allowed
     * @return Result
     */
    public function setAllowedValues(array $allowed) : Result;

    /**
     * Set the value
     *
     * @param $value
     * @return Result
     */
    public function setValue($value) : Result;

    /**
     * Set the exception to throw
     *
     * @param Exception $exception
     * @return Result
     */
    public function setException(Exception $exception) : Result;
}
