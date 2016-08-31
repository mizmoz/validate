<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
 */

namespace Mizmoz\Validate\Tests\Resolver;

use Mizmoz\Validate\Resolver\ToModel;
use Mizmoz\Validate\Tests\Helper\HasMockeryTrait;
use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Tests\TestModel\User;

class ToModelTest extends TestCase
{
    use HasMockeryTrait;

    public function testResolveToModel()
    {
        // simple single key resolution
        $resolver = new ToModel(User::class);

        $this->assertEquals(1, $resolver->resolve(1)->userId);
        $this->assertInstanceOf(User::class, $resolver->resolve(1));
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
}
