<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class IsSame implements Validator, Validator\Description
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
        if ($value instanceof ValueWasNotSet) {
            $isValid = true;
        } else {
            $isValid = ($this->strict ? $value === $this->match : $value == $this->match);
        }

        return new Result(
            $isValid,
            $value,
            'isSame',
            (! $isValid ? 'Values are not the same' : '')
        );
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return [
            'match' => $this->match,
            'strict' => $this->strict,
        ];
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->getDescription();
    }
}
