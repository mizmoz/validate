<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Contract\Validator;

interface Description extends \JsonSerializable
{
    /**
     * Get a description of the validator
     *
     * @return mixed
     */
    public function getDescription();
}
