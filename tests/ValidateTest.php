<?php
/**
 * @package Mizmoz
 * @copyright Copyright 2016 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests;

use Mizmoz\Validate\Resolver\ToClass;
use Mizmoz\Validate\Validate;

class ValidateTest extends TestCase
{
    public function testSimpleValidation()
    {
        $this->assertTrue(Validate::isNumeric()->validate(123)->isValid());
        $this->assertTrue(Validate::isNumeric()->validate('123')->isValid());

        $this->assertTrue(Validate::isNumeric()->isRequired()->validate(0)->isValid());
        $this->assertTrue(Validate::isNumeric()->isRequired()->validate(123)->isValid());
        $this->assertTrue(Validate::isNumeric()->isRequired()->validate('123')->isValid());

        $this->assertFalse(Validate::isNumeric()->isRequired()->validate('')->isValid());
        $this->assertFalse(Validate::isNumeric()->isRequired()->validate(false)->isValid());
        $this->assertFalse(Validate::isNumeric()->isRequired()->validate(null)->isValid());
    }

    public function testResolveToObject()
    {
        $this->assertEquals(
            (object)['name' => 'Ian'],
            Validate::isArray()->resolveTo(new ToClass(\stdClass::class))->validate(['name' => 'Ian'])->getValue()
        );

        $this->assertEquals(
            (new \DateTime('2016-01-01')),
            Validate::isArray()
                ->resolveTo(new ToClass(\DateTime::class, ToClass::VALUE_IS_PROPERTY_LIST))
                ->validate(['2016-01-01'])
                ->getValue()
        );
    }

    public function testDescriptionOnSetWithString()
    {
        $validator = Validate::set([
            'name' => Validate::isString()
                ->setDescription('Full name'),
        ]);

        $this->assertEquals([
            'name' => [
                'description' => 'Full name',
                'isString' => [
                    'strict' => false,
                ],
            ],
        ], $validator->getDescription());
    }

    public function testDescriptionOnSetWithInteger()
    {
        $validator = Validate::set([
            'age' => Validate::isInteger()
                ->setDescription('Age'),
        ]);

        $this->assertEquals([
            'age' => [
                'description' => 'Age',
                'isInteger' => [
                    'strict' => false,
                ],
            ],
        ], $validator->getDescription());
    }

    public function testDescriptionOnSetWithArrayOfShape()
    {
        $validator = Validate::set([
            'templates' => Validate::isArrayOfShape([
                'html' => Validate::isString()
                    ->setDescription('HTML template'),
                'text' => Validate::isString()
                    ->setDescription('Text only template')
            ])->setDescription('Email templates'),
        ]);

        $this->assertEquals([
            'templates' => [
                'description' => 'Email templates',
                'isArrayOfShape' => [
                    'html' => [
                        'description' => 'HTML template',
                        'isString' => [
                            'strict' => false,
                        ]
                    ],
                    'text' => [
                        'description' => 'Text only template',
                        'isString' => [
                            'strict' => false,
                        ]
                    ],
                ],
            ],
        ], $validator->getDescription());
    }

    public function testDescriptionOnSetWithArray()
    {
        $validator = Validate::set([
            'variables' => Validate::isArray()
                ->setDescription(
                    'Add some variables'
                )->setDefault([]),
        ]);

        $this->assertEquals([
            'variables' => [
                'description' => 'Add some variables',
                'isArray' => 'isArray',
            ],
        ], $validator->getDescription());
    }

    public function ztestDescriptionOnSet()
    {
        $validator = Validate::set([
            'emailCampaignId' => Validate::isInteger()
                ->setDescription('Email Campaign to send')
                ->isRequired(),

            'emailListId' => Validate::isInteger()
                ->setDescription('The email list the passed subscriber will be added to')
                ->isRequired(),

            'emailId' => Validate::isOneOfType([
                Validate::isInteger(),
                Validate::isEmail(),
                Validate::isArrayOfShape([
                    'emailAddress' => Validate::isEmail(['allowDisposable' => false])
                        ->setDescription('Subscriber email address')
                        ->isRequired(),

                    'emailTitle' => Validate::isString()
                        ->setDescription('Subscriber title'),

                    'emailFirstname' => Validate::isString()
                        ->setDescription('Subscriber first name'),

                    'emailLastname' => Validate::isString()
                        ->setDescription('Subscriber last name'),

                    'emailCreated' => Validate::isDate(['strict' => false])
                        ->setDescription('Date the subscriber was created'),

                    'meta' => Validate::isArray()
                        ->setDescription('Set additional info for the subscriber'),

                    'source' => Validate::isString()
                        ->setDescription(
                            'Source of the email address, use something like website-sign-up etc.'
                        ),
                ])
            ])->setDescription('Either the emailId or email address to send to')
                ->isRequired(),

            'variables' => Validate::isArray()
                ->setDescription(
                    'Extra merge variables to be available in the email template using {{live.variableName}}.'
                )->setDefault([]),

            'templates' => Validate::isArrayOfShape([
                'html' => Validate::isString()
                    ->setDescription('HTML template'),
                'text' => Validate::isString()
                    ->setDescription('Text only template'),
            ])->setDescription(
                'Pass the HTML and Text templates to use. ' .
                'Passing either of these will completely override the template defined in the portal.'
            )->setDefault([])
        ]);
    }
}
