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
    private string $dateFormat;

    /**
     * Date constructor.
     * @param string $time
     * @param DateTimeZone|null $timezone
     * @param string $dateFormat
     * @throws \DateMalformedStringException
     */
    final public function __construct(string $time = 'now', DateTimeZone $timezone = null, string $dateFormat = 'Y-m-d')
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
     * @throws \DateMalformedStringException
     */
    public static function create(string $format, string $time, DateTimeZone $timezone = null): ?Date
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
