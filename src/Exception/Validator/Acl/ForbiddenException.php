<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Exception\Validator\Acl;

/**
 * Class ForbiddenException
 * @package Mizmoz\Validate\Exception\Validator\Acl
 *
 * User is not allowed access to the resource ever. Example being the resource belongs to someone else.
 */
class ForbiddenException extends AclException
{
}
