<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use \Exception;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Exception\RuntimeException;

class Result implements ResultContract
{
    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var array
     */
    private $allowed = [];

    /**
     * @var Exception
     */
    private $exception;

    /**
     * Result constructor.
     *
     * @param bool $isValid
     * @param mixed $value This is the cleaned value, not the original that was validated.
     * @param string $name
     * @param string $message
     * @param Exception $exception
     */
    public function __construct(
        bool $isValid,
        $value,
        string $name = '',
        string $message = '',
        Exception $exception = null
    ) {
        $this->isValid = $isValid;
        $this->value = $value;
        $this->name = $name;
        $this->exception = $exception;

        if ($message) {
            $this->messages = ($name ? [$name => $message] : [$message]);
        }
    }

    /**
     * @inheritdoc
     */
    public function isValid() : bool
    {
        return $this->isValid;
    }

    /**
     * @inheritdoc
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getException() : Exception
    {
        return ($this->exception ? $this->exception : new RuntimeException(current($this->messages)));
    }

    /**
     * @inheritdoc
     */
    public function setAllowedValues(array $allowed) : ResultContract
    {
        $this->allowed = $allowed;
        return $this;
    }

    /**
     * Set the value
     *
     * @param $value
     * @return ResultContract
     */
    public function setValue($value) : ResultContract
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setException(Exception $exception) : ResultContract
    {
        $this->exception = $exception;
        return $this;
    }
}
