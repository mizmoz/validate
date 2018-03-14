<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract\Validator;

/**
 * Interface Acl
 * @package Mizmoz\Validate\Contract\Validator
 */
interface Acl
{
    /**
     * Set the validator to run after a successful ACL test
     *
     * @param $validator
     * @return mixed
     */
    public function setSuccessValidator($validator);
}
