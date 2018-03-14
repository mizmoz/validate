<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\TestCase;

abstract class ValidatorTestCaseAbstract extends TestCase
{
    /**
     * Test using the Description helper to describe the validation
     */
    abstract public function testDescription();

    /**
     * Test the required states work
     */
    abstract public function testIsRequired();

    /**
     * Check the model serialises
     *
     */
    abstract public function testJsonSerialize();
}
