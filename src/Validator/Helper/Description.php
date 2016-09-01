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
            if ($validation = static::getValidationDescription($shape, $useDescription)) {
                $rules[static::getName($shape)] = $validation;
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

            if (! ($validator = static::getValidationDescription($validator))) {
                // skip non validators
                continue;
            }

            $rules[$key] = [
                'description' => '',
            ];

            if (is_array(current($validator))) {
                foreach ($validator as $k => $item) {
                    $rules[$key][$k] = $item;
                }
            } else {
                $rules[$key] = [
                    'description' => '',
                    'validation' => $validator,
                ];
            }
        }

        return $rules;
    }

    /**
     * Get the validation description for the item
     *
     * @param mixed $shape
     * @param bool $useDescription
     * @return mixed
     */
    public static function getValidationDescription($shape, bool $useDescription = true)
    {
        if ($useDescription && $shape instanceof Validator\Description) {
            // instance can describe itself
            return $shape->getDescription();
        } else if ($useDescription && $shape instanceof Resolver\Description) {
            // instance can describe itself
            return $shape->getDescription();
        } else if ($shape instanceof Validator) {
            // fallback by just getting the class name
            return static::getName($shape);
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

    /**
     * Get the validator name
     *
     * @param mixed $object
     * @return string
     */
    public static function getName($object) : string
    {
        if ($object instanceof Validator) {
            $replace = 'Validator';
        } else if ($object instanceof Resolver) {
            $replace = 'Resolver';
        } else {
            return false;
        }

        // fallback by just getting the class name
        return lcfirst(str_replace('Mizmoz\\Validate\\' . $replace . '\\', '', get_class($object)));
    }
}
