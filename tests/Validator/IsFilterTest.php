<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validate;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsFilter;

class IsFilterTest extends ValidatorTestCaseAbstract
{
    public function testFilter()
    {
        $validator = new IsFilter([
            // apply the where using a callback
            '#paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // apply the where using a callback
            '#nearly-paying' => function ($hashTag, $value) {
                return $hashTag . '<>' . $value;
            },

            // use multiple hash tags and use the hash tag value as the where clause value
            '#active|#credit-hold' => 'networkStatus',
        ]);

        // valid item
        $this->assertTrue($validator->validate('#paying')->isValid());

        // check we get the callback from above in an array
        $decorator = $validator->validate('#paying')->getValue();
        $this->assertEquals(function ($hashTag, $value) {
            return $hashTag . '=' . $value;
        }, $decorator['#paying']);

        // callback works as expected?
        $this->assertEquals('#paying=cheese', $decorator['#paying']('#paying', 'cheese'));

        // check basic conversion type tags
        $this->assertEquals([
            'networkStatus' => ['active'],
            'filter' => '',
        ], $validator->validate('#active')->getValue());
    }

    /**
     * Test :isInteger match
     */
    public function testFilterWithValue()
    {
        $validator = Validate::isFilter([
            '@:isInteger' => 'userId'
        ]);

        // single value
        $this->assertEquals([
            'filter' => '',
            'userId' => [
                123
            ],
        ], $validator->validate('@123')->getValue());

        // multiple values
        $this->assertEquals([
            'filter' => '',
            'userId' => [
                123,
                456,
                789
            ],
        ], $validator->validate('@123 @456 @789')->getValue());
    }

    /**
     * Test we can use a value when filtering like #test=1
     */
    public function testTagWithValue()
    {
        return $this->markTestSkipped('Need to implement test for ' . __METHOD__);

        $validate = Validate::isFilter(['#test']);

        // single value
        $this->assertEquals([
            'filter' => '',
            'test' => [
                '1',
            ],
        ], $validate->validate('#test=1')->getValue());
    }

    /**
     * Test the is required is behaving correctly.
     */
    public function testIsRequired()
    {
        $validate = Validate::isFilter(['#cheese', '#yolo']);

        $this->assertTrue($validate->validate('#cheese')->isValid());
        $this->assertTrue($validate->validate(new ValueWasNotSet())->isValid());

        // make required to check failure
        $validate->isRequired();
        $this->assertFalse($validate->validate('')->isValid());
    }

    /**
     * Test the filter will ignore email addresses
     */
    public function testFilterWithEmail()
    {
        $validate = Validate::isFilter(['@:isInteger' => 'id']);
        $result = $validate->validate('support@mizmoz.com');
        $value = $result->getValue();

        // should not touch the email address
        $this->assertEquals('support@mizmoz.com', $value);
        $this->assertTrue($result->isValid());

        $validate = Validate::isFilter(['@:isInteger' => 'id']);
        $result = $validate->validate('support@mizmoz.com @12345');

        // should not touch the email address
        $this->assertEquals([
            'filter' => 'support@mizmoz.com',
            'id' => [
                12345,
            ]
        ], $result->getValue());
        $this->assertTrue($result->isValid());
    }

    /**
     * Allow specifying a set of tags with one set as the default when none are present
     */
    public function testFilterWithDefaultTag()
    {
        $validator = new IsFilter([
            // use multiple hash tags and use the hash tag value as the where clause value
            '#active*|#credit-hold' => 'networkStatus',
        ]);

        $result = $validator->validate('');

        // valid item
        $this->assertEquals([
            'filter' => '',
            'networkStatus' => [
                'active',
            ]
        ], $result->getValue());
        $this->assertTrue($result->isValid());

        /**
         * With a tag used
         */
        $result = $validator->validate('#credit-hold');

        // valid item
        $this->assertEquals([
            'filter' => '',
            'networkStatus' => [
                'credit-hold',
            ]
        ], $result->getValue());
        $this->assertTrue($result->isValid());

        /**
         * With text filter
         */
        $result = $validator->validate('ian');

        // valid item
        $this->assertEquals([
            'filter' => 'ian',
            'networkStatus' => [
                'active',
            ]
        ], $result->getValue());
        $this->assertTrue($result->isValid());
    }

    /**
     * Testing we always get the defaults even with multiple groups
     */
    public function testFilterWithDefaultTagAndOtherTagGroup()
    {
        /**
         * With another tag
         */
        $validator = new IsFilter([
            // use multiple hash tags and use the hash tag value as the where clause value
            '#active*|#credit-hold' => 'networkStatus',

            // some other tag
            '#good|#bad' => 'rating',
        ]);

        $result = $validator->validate('#good');

        // valid item
        $this->assertEquals([
            'filter' => '',
            'rating' => [
                'good',
            ],
            'networkStatus' => [
                'active',
            ]
        ], $result->getValue());
        $this->assertTrue($result->isValid());
    }

    /**
     * Testing you can use default tags with a call back rather than just a string
     */
    public function testFilterWithDefaultTagAndCallback()
    {
        $callback = function ($tag) {
            return $tag . '-rar';
        };

        $validator = new IsFilter([
            // use multiple hash tags and use the hash tag value as the where clause value
            '#active*|#credit-hold' => $callback,
        ], true);

        $result = $validator->validate('');

        // valid item
        $this->assertEquals([
            'filter' => '',
            '#active' => $callback,
        ], $result->getValue());
        $this->assertTrue($result->isValid());

        $decorator = $result->getValue()['#active'];

        // callback works as expected?
        $this->assertEquals('#active-rar', $decorator('#active'));

        /**
         * With a tag
         */
        $result = $validator->validate('#credit-hold');

        // valid item
        $this->assertEquals([
            'filter' => '',
            '#credit-hold' => $callback,
        ], $result->getValue());
        $this->assertTrue($result->isValid());
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        // Basic validation
        $description = Description::getDescription(new IsFilter());

        $this->assertEquals([
            'isFilter' => [
                'allowed' => [],
            ],
        ], $description);
    }

    /**
     * Test serialisation
     */
    public function testJsonSerialize()
    {
        $this->assertEquals('{"allowed":["#cheese","#yolo"]}', json_encode(new IsFilter([
            '#cheese', '#yolo'
        ])));

        $this->assertEquals('{"allowed":["#paying","#nearly-paying","#active","#credit-hold"]}', json_encode(new IsFilter([
            // apply the where using a callback
            '#paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // apply the where using a callback
            '#nearly-paying' => function ($hashTag, $value) {
                return $hashTag . '=' . $value;
            },

            // use multiple hash tags and use the hash tag value as the where clause value
            '#active|#credit-hold' => 'networkStatus',
        ])));
    }
}
