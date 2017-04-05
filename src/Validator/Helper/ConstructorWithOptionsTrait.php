<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Validator\Helper;


trait ConstructorWithOptionsTrait
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * Get the default options for the Validator
     *
     * @param array $options
     * @return array
     */
    abstract public static function getDefaultOptions(array $options): array;

    /**
     * Constructor with options
     *
     * @param array $options - see default options above
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge(self::getDefaultOptions($options), $options);
    }

    /**
     * Get the option
     *
     * @param string $name
     * @param null $defaultValue
     * @return mixed
     */
    public function option(string $name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $defaultValue;
    }
}
