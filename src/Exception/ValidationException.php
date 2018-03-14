<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
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
