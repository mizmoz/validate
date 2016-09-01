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
    private $tags;

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
            $keys = explode('|', (is_int($key) ? $value : $key));

            foreach ($keys as $k) {
                $v = $value;

                if (! is_array($value) && ! is_callable($value)) {
                    $v = str_replace(['@', '#'], '', $k);
                    $v = [$value => $v];
                }

                $this->tags[$k] = $v;
            }
        }

        // set the allowed tags
        $this->allowed = array_keys($this->tags);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isSet = ! ($value instanceof ValueWasNotSet);

        // parse the value for any filter options
        if ($isSet && preg_match_all('/[#@][^\s]*/i', $value, $result, PREG_PATTERN_ORDER)) {
            // get all the tags
            $tags = array_unique($result[0]);

            $decorators = [];
            foreach ($tags as $tag) {
                // remove the tag from the filter
                $value = trim(str_replace($tag, '', $value));

                // validate the tags
                if ($this->tags && ! isset($this->tags[$tag])) {
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
