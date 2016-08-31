<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Resolver;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Resolver\Helper\CreateObjectTrait;

/**
 * Class ToModel
 *
 * Resolve to a Mizmoz Model - probably not very useful as it is but serves as an example of model resolution
 *
 * @package Mizmoz\Validate\Resolver
 */
class ToModel implements Resolver
{
    use CreateObjectTrait;

    /**
     * @var mixed
     */
    private $class;

    /**
     * @var array
     */
    private $pick;

    /**
     * ToClass constructor.
     * @param string $class Class to resolve the value to
     * @param array $pick Used when an array is passed to pick a selection of the keys rather
     *  than use all of them which is the default
     */
    public function __construct(string $class, array $pick = [])
    {
        $this->class = $class;
        $this->pick = $pick;
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        // create the class
        $class = static::createObject($this->class);

        if (is_array($value)) {
            // value is an array to we'll need to apply all of the items to the where
            $value = ($this->pick ? array_intersect($this->pick, array_keys($value)) : $value);

            $model = $class->populate($value)
                ->get();
        } else {
            // basic get the model
            $model = $class->get($value);
        }

        return $model;
    }
}
