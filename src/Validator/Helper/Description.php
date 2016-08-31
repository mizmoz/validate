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

namespace Mizmoz\Validate\Validator\Helper;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Validate;

class Description
{
    /**
     * @param Validator\Description[]|Validator[] $shapes
     * @param bool $useDescription
     * @return array
     */
    public static function getDescription($shapes, bool $useDescription = true) : array
    {
        $rules = [];

        $shapes = (! is_array($shapes) ? [$shapes] : $shapes);

        foreach ($shapes as $shape) {
            if ($validation = static::getValidation($shape, $useDescription)) {
                $rules[] = $validation;
            }
        }

        return $rules;
    }

    /**
     * Get the description for the shapes
     *
     * @param array $shapes
     * @return array
     */
    public static function getDescriptionForShapes(array $shapes) : array
    {
        $rules = [];

        foreach ($shapes as $key => $shape) {
            $validator = Validate::resolveToValidator($shape, $key);
            if (! ($validation = static::getValidation($validator))) {
                // skip non validators
                continue;
            }

            if (is_array(current($validation))) {
                // this is an item like a shape which can have children]
                $items = $validation;

                $children = [];
                $validation = [];
                foreach ($items as $item) {
                    if (is_array($item)) {
                        $children[key($item)] = current($item);
                    } else {
                        $validation[] = $item;
                    }
                }

                $rules[$key] = [
                    'description' => '',
                    'validation' => $validation,
                    'children' => $children,
                ];
            } else {
                $rules[$key] = [
                    'description' => '',
                    'validation' => $validation,
                ];
            }
        }

        return $rules;
    }

    /**
     * Get the validation for the item
     *
     * @param mixed $shape
     * @param bool $useDescription
     * @return mixed
     */
    public static function getValidation($shape, bool $useDescription = true)
    {
        if ($useDescription && $shape instanceof Validator\Description) {
            // instance can describe itself
            return $shape->getDescription();
        } else if ($shape instanceof Validator) {
            // fallback by just getting the class name
            return lcfirst(str_replace('Mizmoz\\Validate\\Validator\\', '', get_class($shape)));
        } else if ($shape instanceof Resolver) {
            // skip resolver
            return false;
        } else {
            throw new \InvalidArgumentException(
                '$shapes must be an array of Mizmoz\Validate\Contract\Validator ' .
                'or Mizmoz\Validate\Contract\Validator\Description. ' . get_class($shape) . ' was provided'
            );
        }
    }
}
