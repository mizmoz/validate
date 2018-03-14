<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Helper;

use Mizmoz\Validate\Exception\RuntimeException;

/**
 * Class ArrayAccess
 * @package Mizmoz\Validate\Validator\Helper
 */
class ArrayAccess implements \ArrayAccess, \Iterator, \JsonSerializable
{
    /**
     * @var Object
     */
    private $object;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     */
    private $index = [];

    /**
     * ArrayAccess constructor.
     * @param $object Object to expose with array accessor
     */
    public function __construct($object)
    {
        if (! is_object($object)) {
            throw new RuntimeException('$object must be some kind of object!');
        }

        $this->object = $object;

        // figure out how bit the object is
        foreach ($object as $key => $value) {
            $this->index[$this->count] = $key;
            $this->count++;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->object->$offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset) ? $this->object->$offset : new ValueWasNotSet());
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->object->$offset = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->object->$offset);
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        // find the offset from the index
        $offset = $this->index[$this->position];

        // return the value
        return $this->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->index[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->index[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return the object as an array
     *
     * @return array
     */
    public function toArray() : array
    {
        $values = [];
        foreach ($this as $key => $value) {
            if ($value instanceof ArrayAccess) {
                $value = $value->toArray();
            }

            $values[$key] = $value;
        }

        return $values;
    }
}
