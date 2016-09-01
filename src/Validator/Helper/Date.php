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

use \DateTime;
use \DateTimeZone;

class Date extends DateTime
{
    /**
     * @var string
     */
    private $dateFormat;

    /**
     * Date constructor.
     * @param string $time
     * @param DateTimeZone $timezone
     * @param string $dateFormat
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null, $dateFormat = 'Y-m-d')
    {
        if (! $timezone) {
            $timezone = new DateTimeZone('UTC');
        }

        parent::__construct($time, $timezone);

        // set the date format
        $this->dateFormat = $dateFormat;
    }

    /**
     * Set the date format
     *
     * @param string $dateFormat
     * @return Date
     */
    public function setDateFormat(string $dateFormat) : Date
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * Create the Date from the format
     *
     * @param string $format
     * @param string $time
     * @param DateTimeZone|null $timezone
     * @return Date|null
     */
    public static function createFromFormat($format, $time, DateTimeZone $timezone = null)
    {
        $dateTime = parent::createFromFormat($format, $time, $timezone);

        if (! $dateTime) {
            return null;
        }

        return new static(
            '@' . $dateTime->getTimestamp(),
            $timezone,
            $format
        );
    }

    /**
     * Get the date as a string
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->format($this->dateFormat);
    }
}
