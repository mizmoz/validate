<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsEmail implements Validator
{
    /**
     * @var bool
     */
    private $strict;

    /**
     * IsEmail constructor.
     *
     * @param bool $strict Check DNS records for a mail server at the domain
     */
    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet);
        $message = '';

        if (! $isValid) {
            // check the address looks something like an email
            $message = $this->checkAddress($value);
            $isValid = ! $message;
        }

        return new Result(
            $isValid,
            $value,
            'isString',
            $message
        );
    }

    /**
     * Check the address, if a message is returned that means it failed
     *
     * @param $value
     * @return string
     */
    private function checkAddress($value) : string
    {
        if (! is_string($value)) {
            // no even a string, bad email!
            return 'This is not a valid email address';
        }

        $email = idn_to_utf8($value);

        if (! ($parts = $this->splitEmailParts($email))) {
            // couldn't parse the local@hostname
            return 'This is not a valid email address';
        }

        return '';
    }

    /**
     * Splits the given value in hostname and local part of the email address
     *
     * @param string $value Email address to be split
     * @return bool Returns false when the email can not be split
     */
    private function splitEmailParts($value)
    {
        $value = is_string($value) ? $value : '';
        // Split email address up and disallow '..'
        if (strpos($value, '..') !== false
            || ! preg_match('/^(.+)@([^@]+)$/', $value, $matches)
        ) {
            return false;
        }

        return [
            'local' => $matches[1],
            'hostname' => $matches[2],
        ];
    }
}
