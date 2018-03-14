<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Exception;

class NonUniqueHelperNameException extends RuntimeException
{
    /**
     * @var string
     */
    private $helperName;

    /**
     * Get the helper name
     *
     * @return string
     */
    public function getHelperName() : string
    {
        return $this->helperName;
    }

    /**
     * Set the helper name
     *
     * @param string $helperName
     * @return NonUniqueHelperNameException
     */
    public function setHelperName(string $helperName) : NonUniqueHelperNameException
    {
        $this->helperName = $helperName;
        return $this;
    }
}
