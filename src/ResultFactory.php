<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Exception\Validator\Acl\ForbiddenException;

class ResultFactory
{
    /**
     * Create a result for a forbidden acl test
     *
     * @param string $message
     * @param string $name
     * @return ResultContract
     */
    public static function aclForbidden(string $message, string $name = '') : ResultContract
    {
        return new Result(false, '', $name, $message, new ForbiddenException($message));
    }
}
