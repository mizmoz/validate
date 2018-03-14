<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
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

    /**
     * Test the ability to mock the results of a validator
     */
    public function testMocking()
    {
        // Mock the IsReCaptcha item once and return a passed result with ok-value set
        ValidatorFactory::mock('isReCaptcha')
            ->valid()
            ->value('ok-value')
            ->message('hurray');

        /** @var Result $result */
        $result = ValidatorFactory::isReCaptcha()->validate('this-is-a-test');

        $this->isTrue($result->isValid());
        $this->assertSame('ok-value', $result->getValue());
        $this->assertSame('isReCaptcha', $result->getName());
        $this->assertSame(['isReCaptcha' => 'hurray'], $result->getMessages());

        // Perform test again... we should get the same result
        $result = ValidatorFactory::isReCaptcha()->validate('this-is-a-test');
        $this->isTrue($result->isValid());
    }

    /**
     * Mock should only exist for a single call
     */
    public function testMockingOnce()
    {
        // Mock the isString item once and return a passed result with ok-value set
        ValidatorFactory::mock('isString')
            ->valid(false)
            ->value('fail-value')
            ->message('boo')
            ->once();

        /** @var Result $result */
        $result = ValidatorFactory::isString()->validate('this-is-a-test');
        $this->isFalse($result->isValid());
        $this->assertSame('fail-value', $result->getValue());
        $this->assertSame('isString', $result->getName());
        $this->assertSame(['isString' => 'boo'], $result->getMessages());

        // this should return true now as we're calling the original validator
        $result = ValidatorFactory::isString()->validate('this-is-a-test');
        $this->isTrue($result->isValid());
        $this->assertSame('this-is-a-test', $result->getValue());
        $this->assertSame('isString', $result->getName());
        $this->assertSame([], $result->getMessages());
    }
}
