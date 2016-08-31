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

namespace Mizmoz\Validate\Exception\Validator\Acl;

/**
 * Class NotPrivilegedException
 * @package Mizmoz\Validate\Exception\Validator\Acl
 *
 * The user could have access to the resource if they had more privileges
 */
class NotPrivilegedException extends ForbiddenException
{
}
