<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Exception\InvalidHelperTypeException;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\IsSame;
use Mizmoz\Validate\ValidatorFactory;

class ValidatorFactoryTest extends TestCase
{
    public function testAddCustomHelperCallback()
    {
        // Create a new validator resolver
        ValidatorFactory::setHelper('isIan', function () : Validator {
            return new class () implements Validator {
                public function validate($value) : ResultContract
                {
                    return new Result(
                        $value === 'Ian',
                        $value
                    );
                }
            };
        });

        // Test the validator works
        $this->assertTrue(ValidatorFactory::isIan()->validate('Ian')->isValid());

        // not Ian
        $this->assertFalse(ValidatorFactory::isIan()->validate('Bob')->isValid());
    }

    /**
     * Add some custom validators
     */
    public function testAddCustomHelperClass()
    {
        ValidatorFactory::setHelperClass('isAnotherSame', IsSame::class);

        // Test the validator works
        $this->assertTrue(ValidatorFactory::isAnotherSame('cheese')->validate('cheese')->isValid());

        // not Ian
        $this->assertFalse(ValidatorFactory::isAnotherSame('cats')->validate('dogs')->isValid());

        // test extra arguments are passed correctly
        $this->assertTrue(ValidatorFactory::isAnotherSame(0)->validate(false)->isValid());
        $this->assertFalse(ValidatorFactory::isAnotherSame(0, true)->validate(false)->isValid());
    }

    /**
     * Check we're not able to add bad helpers to the list
     */
    public function testAddCustomHelperValidation()
    {
        // make sure we can only passed callables
        $this->expectException(\TypeError::class);

        // this is a list of non callable items
        ValidatorFactory::setHelper('isIan', null);
        ValidatorFactory::setHelper('isIan', false);
        ValidatorFactory::setHelper('isIan', '');

        // check we can only pass callable items that return a valid type
        $this->expectException(InvalidHelperTypeException::class);

        // bad types
        ValidatorFactory::setHelper('isIan', new Result(true, true));
        ValidatorFactory::setHelper('isIan', function () {
        });
    }
}
