<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Resolver\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class ToDefaultValue implements Resolver, Description
{
    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private $strict;

    /**
     * ToDefaultValue constructor.
     *
     * @param $defaultValue
     * @param bool $strict
     */
    public function __construct($defaultValue, bool $strict = true)
    {
        $this->defaultValue = $defaultValue;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        if ($value instanceof ValueWasNotSet || (! $this->strict && ! $value)) {
            // value is not set so use the default value
            $defaultValue = $this->defaultValue;

            // check if instance of Closure first as this doesn't fire the autoloader.
            $value = ($defaultValue instanceof \Closure && is_callable($defaultValue)
                ? $defaultValue($value)
                : $defaultValue
            );
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
