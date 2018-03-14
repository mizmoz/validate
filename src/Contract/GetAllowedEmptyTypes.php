<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract;

interface GetAllowedEmptyTypes
{
    /**
     * Get a list of the allowed empty types for checking if something passes an isRequired test
     *
     * @return array
     */
    public function getAllowedEmptyTypes() : array;
}
