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

namespace Mizmoz\Validate\Tests\TestModel;

class User
{
    /**
     * @var mixed
     */
    public $userId;

    /**
     * @var string
     */
    public $userStatus = '';

    /**
     * @var int
     */
    public static $currentUserId = 123456;

    /**
     * Get the user by id
     *
     * @param mixed $userId
     * @return User
     */
    public static function get($userId = null) : User
    {
        $user = new self;
        $user->userId = $userId;
        return $user;
    }

    /**
     * Get the current user
     *
     * @return User
     */
    public static function current() : User
    {
        return static::get(self::$currentUserId);
    }
}
