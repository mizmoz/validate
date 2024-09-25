<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Helper;

trait HasMockeryTrait
{
    /**
     * Set strict errors off and return the mock object
     *
     * @param $argument
     * @return \Mockery\MockInterface
     */
    public function mock($argument)
    {
        if (defined('E_STRICT')) {
            error_reporting(E_ALL & E_STRICT);
        }

        return \Mockery::mock($argument);
    }
}
