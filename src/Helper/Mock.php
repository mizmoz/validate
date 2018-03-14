<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Helper;

use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\ValidatorFactory;
use ReflectionClass;

class Mock implements Validator, Resolver
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Validator/string
     */
    private $mockedValidator;

    /**
     * @var bool
     */
    private $once = false;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var array
     */
    private $result = [
        'message' => '',
        'valid' => true,
        'value' => '',
    ];

    /**
     * Init with the name of the validator to mock and it's current value
     *
     * @param string $name
     * @param $mockedValidator
     */
    public function __construct(string $name, $mockedValidator)
    {
        $this->name = $name;
        $this->mockedValidator = $mockedValidator;
    }

    /**
     * Get the mocked validator
     *
     * @return Validator
     */
    public function getMockedValidator()
    {
        return $this->mockedValidator;
    }

    /**
     * Get the arguments the validator was called with
     *
     * @return array
     */
    public function getCallArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Set the validity of the result
     *
     * @param bool $valid
     * @return Mock
     */
    public function valid(bool $valid = true): Mock
    {
        $this->result['valid'] = $valid;
        return $this;
    }

    /**
     * Set the value of the result
     *
     * @param mixed $value
     * @return Mock
     */
    public function value($value): Mock
    {
        $this->result['value'] = $value;
        return $this;
    }

    /**
     * Set the returned message
     *
     * @param string $message
     * @return Mock
     */
    public function message(string $message): Mock
    {
        $this->result['message'] = $message;
        return $this;
    }

    /**
     * Only use this override once and once done set the original validator back
     *
     * @return Mock
     */
    public function once(): Mock
    {
        $this->once = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ResultContract
    {
        $this->arguments[__FUNCTION__] = ['value' => $value];

        if ($this->once) {
            $this->unMock();
        }

        return new Result(
            $this->result['valid'],
            $this->result['value'],
            $this->name,
            $this->result['message']
        );
    }

    /**
     * @inheritdoc
     */
    public function resolve($value)
    {
        $this->arguments[__FUNCTION__] = ['value' => $value];

        if ($this->once) {
            $this->unMock();
        }

        return new Result(
            $this->result['valid'],
            $this->result['value'],
            $this->name,
            $this->result['message']
        );
    }

    /**
     * Reset the mock
     *
     * @return Mock
     */
    public function reset(): Mock
    {
        $this->once = false;
        $this->arguments = [];
        $this->result = [
            'message' => '',
            'valid' => true,
            'value' => '',
        ];

        return $this;
    }

    /**
     * Remove the Mock from the ValidatorFactory and reset to it's original state
     *
     * @return Mock
     */
    public function unMock(): Mock
    {
        ValidatorFactory::unMock($this->name);
        return $this;
    }

    /**
     * Store the call arguments
     *
     * @return $this
     * @throws \ReflectionException
     */
    public function __invoke()
    {
        // get the arguments
        $args = func_get_args();
        $values = [];

        // get the constructor params
        $params = (new ReflectionClass($this->mockedValidator))->getConstructor()->getParameters();

        foreach ($params as $key => $param) {
            if (isset($args[$key])) {
                $values[$param->name] = $args[$key];
            } else if ($param->isDefaultValueAvailable()) {
                $values[$param->name] = $param->getDefaultValue();
            }
        }

        $this->arguments['__constructor'] = $values;
        return $this;
    }
}