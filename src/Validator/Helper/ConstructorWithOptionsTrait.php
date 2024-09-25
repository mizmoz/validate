<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Validator\Helper;

trait ConstructorWithOptionsTrait
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * Get the default options for the Validator
     *
     * @param array $options
     * @return array
     */
    abstract public static function getDefaultOptions(array $options): array;

    /**
     * Constructor with options
     *
     * @param array $options - see default options above
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge(self::getDefaultOptions($options), $options);
    }

    /**
     * Get the option
     *
     * @param string $name
     * @param null $defaultValue
     * @return mixed
     */
    public function option(string $name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->getDescription();
    }
}
