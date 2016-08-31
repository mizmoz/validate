<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract;

interface GetAllowedPropertyValues
{
    /**
     * Get the allowed values for a property. For instance if a property status is allowed on or off return:
     *  ['on', 'off']
     *
     * And if it's allowed to be null add that to the array:
     *  ['on', 'off', null]
     *
     * @return array
     */
    public function getAllowedPropertyValues() : array;
}
