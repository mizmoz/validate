<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Exception\Validator\Acl;

/**
 * Class NotPrivilegedException
 * @package Mizmoz\Validate\Exception\Validator\Acl
 *
 * The user could have access to the resource if they had more privileges
 */
class NotPrivilegedException extends ForbiddenException
{
}
