<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract;

interface Resolver
{
    /**
     * $value is a single property to be passed to the constructor - like call_user_func
     */
    const VALUE_IS_PROPERTY = 'property';

    /**
     * $value is a list of properties to be passed - like call_user_func_array
     */
    const VALUE_IS_PROPERTY_LIST = 'property-list';

    /**
     * $value is a named list of properties. We'll use reflection to make sure the properties
     * are passed in the correct order to the constructor
     */
    const VALUE_IS_NAMED_PROPERTY_LIST = 'named-property-list';

    /**
     * Resolve the provider value to a new type
     *
     * @param $value
     * @return mixed
     */
    public function resolve($value);
}
