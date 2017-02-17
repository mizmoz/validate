<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsFilter implements Validator, Validator\Description
{
    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var
     */
    private $defaults = [];

    /**
     * @var array
     */
    private $allowed = [];

    /**
     * IsFilter constructor.
     *
     * @param array $tags
     */
    public function __construct(array $tags = [])
    {
        $this->setTags($tags);
    }

    /**
     * Set the tags allowed in this filter
     *
     * @param array $tags
     * @return IsFilter
     */
    public function setTags(array $tags) : IsFilter
    {
        // parse the tags
        foreach ($tags as $key => $value) {
            $group = [
                'default' => null,
                'tags' => [],
            ];
            $keys = explode('|', (is_int($key) ? $value : $key));

            foreach ($keys as $k) {
                $v = $value;

                if (! is_array($value) && ! is_callable($value)) {
                    $v = str_replace(['@', '#'], '', $k);

                    if (strpos($v, '*') !== false) {
                        $v = str_replace('*', '', $v);
                        $group['default'] = $v;
                    }

                    $group['tags'][] = $v;
                    $v = [$value => $v];
                }

                $this->tags[$k] = $v;
            }

            if (isset($group['default'])) {
                // add the groups
                $this->defaults[$value] = $group;
            }
        }

        // set the allowed tags
        $this->allowed = array_keys($this->tags);

        return $this;
    }

    /**
     * Set any default tags
     *
     * @param $value
     * @return array|string
     */
    private function setDefaults($value)
    {
        foreach ($this->defaults as $key => $defaults) {
            if (is_string($value)) {
                $value = [
                    'filter' => $value,
                    $key => [
                        $defaults['default']
                    ],
                ];
            } else if (is_array($value)) {
                $value[$key] = [
                    $defaults['default']
                ];
            }
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isSet = ! ($value instanceof ValueWasNotSet);

        // parse the value for any filter options
        if ($isSet && preg_match_all('/(^|\s)([#@][^\s]*)/i', $value, $result, PREG_PATTERN_ORDER)) {
            // get all the tags
            $tags = array_unique($result[2]);

            $decorators = [];
            foreach ($tags as $tag) {
                $type = substr($tag, 0, 1);

                // remove the tag from the filter
                $value = trim(str_replace($tag, '', $value));

                // validate the tags
                if ($this->tags && ! isset($this->tags[$tag])) {
                    $tagValue = substr($tag, 1);

                    // handle special @:isInteger or #:isInteger fields
                    if (isset($this->tags[$type . ':isInteger']) && (new IsInteger())->validate($tagValue)) {
                        $name = key($this->tags[$type . ':isInteger']);
                        $decorators[$name] = (isset($decorators[$name]) ? $decorators[$name] : []);
                        $decorators[$name][] = (new IsInteger())->validate($tagValue)->getValue();
                        continue;
                    }

                    // skip not allowed tag
                    continue;
                }

                if (is_callable($this->tags[$tag])) {
                    // callback
                    $decorators[$tag] = $this->tags[$tag];
                } else {
                    $name = key($this->tags[$tag]);
                    $val = current($this->tags[$tag]);

                    $decorators[$name] = (isset($decorators[$name]) ? $decorators[$name] : []);
                    $decorators[$name][] = $val;
                }
            }

            // set the filter string
            $decorators['filter'] = $value;

            // update the value with the decorators
            $value = $decorators;
        }

        $value = $this->setDefaults($value);

        return new Result(
            true,
            $value,
            'isFilter'
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [
            'allowed' => $this->allowed,
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }
}
