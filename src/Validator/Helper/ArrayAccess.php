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

use Mizmoz\Validate\Exception\RuntimeException;

/**
 * Class ArrayAccess
 * @package Mizmoz\Validate\Validator\Helper
 */
class ArrayAccess implements \ArrayAccess, \Iterator
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
        return ($this->offsetExists($offset) ? $this->object->$offset : null);
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
}
