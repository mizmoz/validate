<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Exception\Validator\Acl;

/**
 * Class NotActiveException
 * @package Mizmoz\Validate\Exception\Validator\Acl
 *
 * The user account that is trying to access the resource is not active.
 * Could be needing to confirm details, deleted etc.
 */
class NotActiveException extends AclException
{
}
