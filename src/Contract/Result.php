<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
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
