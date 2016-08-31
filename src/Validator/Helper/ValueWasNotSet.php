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

namespace Mizmoz\Validate\Validator\Helper;

/**
 * This is just used as a way of passing something to a validator to say the valud was not set.
 *
 * For example in the IsArrayOfShape validator we might have a non required field IsString but passing null will make
 * the validator fail the test. Passing ValueWasNotSet allows IsString to handle the validation how it sees fit.
 *
 * @package Mizmoz\Validate\Validator\Helper
 */
class ValueWasNotSet
{
}
