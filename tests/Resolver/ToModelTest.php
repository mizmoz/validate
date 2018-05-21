<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Resolver;

use Mizmoz\Validate\Resolver\ToModel;
use Mizmoz\Validate\Tests\Helper\HasMockeryTrait;
use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Tests\TestModel\User;
use Mizmoz\Validate\Validator\Helper\Description;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

class ToModelTest extends TestCase
{
    use HasMockeryTrait;

    public function testResolveToModel()
    {
        // simple single key resolution
        $resolver = new ToModel(User::class);

        $this->assertEquals(1, $resolver->resolve(1)->userId);
        $this->assertInstanceOf(User::class, $resolver->resolve(1));

        // resolution with no value
        $value = new ValueWasNotSet;
        $this->assertEquals($value, $resolver->resolve($value));
    }

    public function testResolveArrayToModel()
    {
        $value = ['userId' => 321, 'userStatus' => 'active'];
        $user = new User();
        $user->userId = $value['userId'];
        $user->userStatus = $value['userStatus'];

        // setup the mock user object
        $userMock = $this->mock(User::class);
        $userMock->shouldReceive('populate')
            ->with($value)
            ->andReturn($userMock);
        $userMock->shouldReceive('get')
            ->andReturn($user);

        ToModel::setCreateObjectReturnValue($userMock, User::class);

        // resolve an array of details
        $resolver = new ToModel(User::class);

        $result = $resolver->resolve($value);
        $this->assertEquals($value['userId'], $result->userId);
        $this->assertEquals($value['userStatus'], $result->userStatus);
    }

    /**
     * @inheritDoc
     */
    public function testDescription()
    {
        // Basic validation
        $description = Description::getDescription(new ToModel(User::class));

        $this->assertEquals([
            'toModel' => [
                'class' => User::class,
            ],
        ], $description);
    }
}
