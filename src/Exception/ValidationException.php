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

class ValidationException extends \RuntimeException
{
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * Add a message to the exception
     *
     * @param string $message
     * @return ValidationException
     */
    public function addMessage(string $message): ValidationException
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set the validator messages
     *
     * @param array $messages
     * @return ValidationException
     */
    public function setMessages(array $messages): ValidationException
    {
        $this->messages = $messages;
        return $this;
    }
}
