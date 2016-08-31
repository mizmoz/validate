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

class IsSame implements Validator
{
    /**
     * @var mixed
     */
    private $match;

    /**
     * @var bool
     */
    private $strict;

    /**
     * IsSame constructor.
     * @param mixed $match
     * @param bool $strict
     */
    public function __construct($match, bool $strict = false)
    {
        $this->match = $match;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function validate($value) : ResultContract
    {
        $isValid = ($this->strict ? $value === $this->match : $value == $this->match);

        return new Result(
            $isValid,
            $value,
            'isSame',
            (! $isValid ? 'Values are not the same' : '')
        );
    }
}
