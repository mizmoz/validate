<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use \Exception;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Exception\ValidationException;

class ResultContainer implements ResultContract
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isValid = true;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array
     */
    private $results = [];

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
     * ResultContainer constructor.
     *
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    /**
     * Add another result object to the result set. If a name is passed the messages will be separated in
     * to a keyed array.
     *
     * @param ResultContract $result
     * @param string $name
     * @return ResultContainer
     */
    public function addResult(ResultContract $result, string $name = ''): ResultContainer
    {
        $this->results[] = $result;
        $this->isValid = ($this->isValid ? $result->isValid() : false);

        if ($name) {
            if (! isset($this->messages[$name])) {
                $this->messages[$name] = [];
            }

            $messages = &$this->messages[$name];
        } else {
            $messages = &$this->messages;
        }

        foreach ($result->getMessages() as $key => $message) {
            if (! is_numeric($key)) {
                $messages[$key] = $message;
            } else {
                $messages[] = $message;
            }
        }

        // update the value
        $this->value = $result->getValue();

        if (! $result->isValid()) {
            $this->addException($name, $result);
        }

        return $this;
    }

    /**
     * Add an array of results
     *
     * @param array $results
     * @return ResultContainer
     */
    public function addResults(array $results) : ResultContainer
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }

        return $this;
    }

    /**
     * Add the exception details
     *
     * @param string $name
     * @param ResultContract $result
     * @return ResultContainer
     */
    public function addException(string $name, ResultContract $result): ResultContainer
    {
        if (! $this->exception) {
            $this->exception = new ValidationException();
        }

        if ($name) {
            foreach ($result->getMessages() as $message) {
                $this->exception->addMessage($message . ' for ' . $name);
            }

            return $this;
        }

        $this->exception->addMessage($result->getException()->getMessage());
        return $this;
    }

    /**
     * Was the validation valid?
     *
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->isValid;
    }

    /**
     * Get the validated value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the messages
     *
     * @return array
     */
    public function getMessages() : array
    {
        return array_filter($this->messages, function ($value) {
            return $value;
        });
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
     * @inheritDoc
     */
    public function getException() : Exception
    {
        return $this->exception;
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
     * S@inheritdoc
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
