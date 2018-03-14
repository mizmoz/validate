<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;

class ToMappedValue implements Resolver
{
    /**
     * @var array
     */
    private $map;

    /**
     * Init with the map. The map should be an array of arrays like [['from' => 1, 'to' => 'one']]
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        foreach ($this->map as $map) {
            if ($map['from'] === $value) {
                $newValue = $map['to'];
                return (is_callable($newValue) ? $newValue($value) : $newValue);
            }
        }

        return $value;
    }
}
