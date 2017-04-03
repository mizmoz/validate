<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Resolver;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Resolver\ToDefaultValue;
use Mizmoz\Validate\Resolver\ToModel;
use Mizmoz\Validate\Validator\Helper\Description;

class Chain implements Validator\Description, Validator
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Validator chain
     *
     * @var Validator[]
     */
    private $chain = [];

    /**
     * @var bool
     */
    private $breakOnError;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var string
     */
    private $characterEncoding;

    /**
     * Chain constructor.
     *
     * @param Validator $validator
     * @param bool $breakOnError
     */
    public function __construct(Validator $validator, $breakOnError = true)
    {
        $this->validator = $validator;
        $this->chain[] = $validator;
        $this->breakOnError = $breakOnError;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Chain
     */
    public function setDescription(string $description) : Chain
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set the required flag
     *
     * @param array $allowedEmptyTypes
     * @return Chain
     */
    public function isRequired($allowedEmptyTypes = null) : Chain
    {
        $this->chain[] = ValidatorFactory::isRequired($this->validator, $allowedEmptyTypes);
        return $this;
    }

    /**
     * Set the default value, this works the same as the resolvers so be careful where you place this in the chain
     *
     * @param $defaultValue
     * @param bool $strict
     * @return Chain
     */
    public function setDefault($defaultValue, bool $strict = true) : Chain
    {
        $this->chain[] = new ToDefaultValue($defaultValue, $strict);
        return $this;
    }

    /**
     * Resolve the values to something else
     *
     * @param Resolver $resolver
     * @return Chain
     */
    public function resolveTo(Resolver $resolver) : Chain
    {
        $this->chain[] = $resolver;
        return $this;
    }

    /**
     * Resolve the values to a model
     *
     * @param string $model
     * @param array $paramMap For example ['me' => User::current()->userId]
     * @return Chain
     */
    public function resolveToModel(string $model, array $paramMap = []) : Chain
    {
        $this->chain[] = new ToModel($model, $paramMap);
        return $this;
    }

    /**
     * Validate the number range
     *
     * @param int $min
     * @param int $max
     * @return Chain
     */
    public function numberIsRange(int $min = null, int $max = null) : Chain
    {
        $this->chain[] = ValidatorFactory::numberIsRange($min, $max);
        return $this;
    }

    /**
     * Validate the string length
     *
     * @param int $min
     * @param int $max
     * @param null|string $encoding
     * @return Chain
     */
    public function textIsLength(int $min = 0, int $max = 0, $encoding = null) : Chain
    {
        $encoding = ($encoding ? $encoding : $this->characterEncoding);
        $this->chain[] = ValidatorFactory::textIsLength($min, $max, $encoding);
        return $this;
    }

    /**
     * Set the default character encoding to use
     *
     * @param $encoding
     * @return Chain
     */
    public function setCharacterEncoding($encoding) : Chain
    {
        $this->characterEncoding = $encoding;
        return $this;
    }

    /**
     * @param $value
     * @return ResultContract
     */
    public function validate($value) : ResultContract
    {
        $resultContainer = new ResultContainer;

        foreach ($this->chain as $item) {
            if ($item instanceof Resolver) {
                // resolve the value to something else
                $value = $item->resolve($value);

                // update the value of the result object
                $resultContainer->setValue($value);

                continue;
            }

            // get the validator result
            $result = $item->validate($value);

            // add to the container
            $resultContainer->addResult($result);

            // update the value
            $value = $result->getValue();

            if (! $resultContainer->isValid() && $this->breakOnError) {
                // validation error we break on error
                break;
            }
        }

        // add the results to the result set
        return $resultContainer;
    }

    /**
     * Get the chain
     *
     * @return Chain[]
     */
    public function getChain() : array
    {
        return $this->chain;
    }

    /**
     * Get the original validator
     *
     * @return Validator
     */
    public function getValidator() : Validator
    {
        return $this->validator;
    }

    /**
     * Call the methods either on the ValidatorFactory or original validator used to create the chain
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // first check if a help by this name exists in the ValidatorFactory
        if (ValidatorFactory::helperExists($name)) {
            $this->chain[] = call_user_func_array(ValidatorFactory::class . '::' . $name, $arguments);
            return $this;
        }

        // does the method exist on the first validator?
        if (! method_exists($this->validator, $name)) {
            throw new \RuntimeException(
                'Method does not exist on validator: ' . get_class($this->validator) . '::' . $name
            );
        }

        return call_user_func_array([$this->validator, $name], $arguments);
    }

    /**
     * @return array
     */
    public function getDescription()
    {
        return Description::getDescription($this->chain);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getDescription();
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->description;
    }
}
