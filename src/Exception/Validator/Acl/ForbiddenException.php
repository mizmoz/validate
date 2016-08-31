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
 * Class ForbiddenException
 * @package Mizmoz\Validate\Exception\Validator\Acl
 *
 * User is not allowed access to the resource ever. Example being the resource belongs to someone else.
 */
class ForbiddenException extends AclException
{
}
