<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
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
    public static function create($format, $time, DateTimeZone $timezone = null)
    {
        if (! $timezone) {
            $timezone = new DateTimeZone('UTC');
        }

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
