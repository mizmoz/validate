<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
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
