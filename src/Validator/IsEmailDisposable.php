<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

/**
 * Check if the passed email address is a disposable account like Guerilla Mail
 * @package Mizmoz\Validate\Validator
 */
class IsEmailDisposable implements Validator, Validator\Description
{
    /**
     * @var array
     */
    private $hosts = [];

    /**
     * IsEmailDisposable constructor.
     */
    public function __construct()
    {
        $this->hosts = require __DIR__ . '/../../resources/is-email-disposable.php';
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet);
        $message = '';

        if (! $isValid) {
            // is this disposable
            $isValid = $this->isDisposable($value);
        }

        return new Result(
            $isValid,
            $value,
            'isEmailDisposable',
            $message
        );
    }

    /**
     * Check the email
     *
     * @param $email
     * @return bool
     */
    private function isDisposable($email) : bool
    {
        // check to see if the hostname appears in the disposable list
        $email = idn_to_utf8($email, 0, INTL_IDNA_VARIANT_UTS46);

        if (preg_match('/^(.+)@([^@]+)$/', $email, $matches)) {
            $parts = explode('.', strtolower($matches[2]));

            while (count($parts) > 1) {
                $host = implode('.', $parts);
                $hash = sha1($host);

                if (isset($this->hosts[$hash])) {
                    // this is disposable
                    return true;
                }

                // remove an item from the beginning
                array_shift($parts);
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
