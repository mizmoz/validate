<?php

/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Tests\Type;

use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Type\Decimal;

class DecimalTest extends TestCase
{
    public function testCreateSingleDigits()
    {
        $decimal = new Decimal('0.08');
        $this->assertSame('0.08', (string)$decimal);
        $this->assertSame(0.08, $decimal->getFloatValue());
        $this->assertSame(8, $decimal->getCents());
        $this->assertSame(8, $decimal->getCents());
    }


    /**
     * Test creating some decimal with varying numbers
     *
     */
    public function testCreateDecimal()
    {
        $this->assertSame('0.00', (string)new Decimal());
        $this->assertSame('0', (string)new Decimal(0, 0));
        $this->assertSame('0.08', (string)new Decimal('0.08'));
        $this->assertSame('1.23', (string)new Decimal('1.23'));
        $this->assertSame('5.99', (string)new Decimal(5.99));
        $this->assertSame('10', (string)new Decimal(10, 0));
        $this->assertSame('10.10', (string)new Decimal(10.1));
    }

    /**
     * Test creating from cents/pence
     */
    public function testCreateDecimalFromCents()
    {
        $this->assertSame('0.00', (string)(Decimal::fromCents(0)));
        $this->assertSame('0.01', (string)(Decimal::fromCents(1)));
        $this->assertSame('0.10', (string)(Decimal::fromCents(10)));
        $this->assertSame('0.45', (string)(Decimal::fromCents('45')));
        $this->assertSame('1.00', (string)(Decimal::fromCents('100')));
        $this->assertSame('1.23', (string)(Decimal::fromCents('123')));
    }

    /**
     * Test getting the cents version of a number
     *
     */
    public function testGetCents()
    {
        $this->assertSame(0, (new Decimal())->getCents());
        $this->assertSame(123, (new Decimal(1.23))->getCents());
        $this->assertSame(87232, (new Decimal(872.32))->getCents());
        $this->assertSame(10010, (new Decimal(100.1))->getCents());
    }

    /**
     * Test getting the integer part of a number
     *
     */
    public function testGetIntegerPart()
    {
        $this->assertSame(0, (new Decimal())->getInteger());
        $this->assertSame(1, (new Decimal(1.23))->getInteger());
        $this->assertSame(872, (new Decimal(872.32))->getInteger());
        $this->assertSame(100, (new Decimal(100.1))->getInteger());
    }

    /**
     * Test getting the fractional part of a number
     *
     */
    public function testGetFractionalPart()
    {
        $this->assertSame(0, (new Decimal())->getFractional());
        $this->assertSame(8, (new Decimal(0.08))->getFractional());
        $this->assertSame(23, (new Decimal(1.23))->getFractional());
        $this->assertSame(32, (new Decimal(872.32))->getFractional());
        $this->assertSame(10, (new Decimal(100.1))->getFractional());
        $this->assertSame(10, (new Decimal(100.10))->getFractional());
        $this->assertSame(10, (new Decimal(100.101))->getFractional());
        $this->assertSame(11, (new Decimal(100.111))->getFractional());
        $this->assertSame(0, (new Decimal(100))->getFractional());
    }

    /**
     * Test getting the float value
     */
    public function testGetFloatValue()
    {
        $this->assertSame(0.0, (new Decimal(0))->getFloatValue());
        $this->assertSame(0.08, (new Decimal(0.08))->getFloatValue());
        $this->assertSame(1.00, (new Decimal(1))->getFloatValue());
        $this->assertSame(11.00, (new Decimal(11))->getFloatValue());
        $this->assertSame(43.34, (new Decimal(43.34))->getFloatValue());
    }

    /**
     * Test adding decimal items together
     *
     */
    public function testAddition()
    {
        $this->assertSame(0, (new Decimal(0))->addDecimal(new Decimal())->getCents());
        $this->assertSame(8, (new Decimal(0))->addDecimal(new Decimal(0.08))->getCents());
        $this->assertSame(5000, (new Decimal(25))->addDecimal(new Decimal(25))->getCents());
        $this->assertSame(123, (new Decimal(1))->addDecimal(new Decimal(0.23))->getCents());
        $this->assertSame(4321, (new Decimal(20))->addDecimal(new Decimal(23.21))->getCents());
        $this->assertSame(12463824, (new Decimal(123984.129))->addDecimal(new Decimal(654.12))->getCents());
        $this->assertSame(309, (new Decimal(1))->addDecimal(new Decimal(2.09))->getCents());
    }

    /**
     * Test subtracting numbers
     */
    public function testSubtraction()
    {
        $this->assertSame(0, (new Decimal(0))->subtractDecimal(new Decimal())->getCents());
        $this->assertSame(92, (new Decimal(1))->subtractDecimal(new Decimal(0.08))->getCents());
        $this->assertSame(71, (new Decimal(1))->subtractDecimal(new Decimal(0.29))->getCents());
        $this->assertSame(58, (new Decimal(1))->subtractDecimal(new Decimal(0.42))->getCents());
        $this->assertSame(0, (new Decimal(1))->subtractDecimal(new Decimal(1))->getCents());
        $this->assertSame(2500, (new Decimal(50))->subtractDecimal(new Decimal(25))->getCents());
        $this->assertSame(10, (new Decimal(10.10))->subtractDecimal(new Decimal(10))->getCents());
        $this->assertSame(123, (new Decimal(600.23))->subtractDecimal(new Decimal(599.00))->getCents());
        $this->assertSame(12398412, (new Decimal(124638.24))->subtractDecimal(new Decimal(654.12))->getCents());
    }

    /**
     * Test getting a percentage of the decimal
     *
     */
    public function testGetPercent()
    {
        $this->assertSame(1000, (new Decimal(100))->getPercent(10)->getCents());
        $this->assertSame(3000, (new Decimal(100))->getPercent(30)->getCents());
        $this->assertSame(1875, (new Decimal(75))->getPercent(25)->getCents());
        $this->assertSame(263, (new Decimal(10.50))->getPercent(25)->getCents());
        $this->assertSame(0, (new Decimal(75))->getPercent(0)->getCents());
    }

    /**
     * Test adding a percentage
     *
     */
    public function testAddPercent()
    {
        $this->assertSame(11000, (new Decimal(100))->addPercent(10)->getCents());
        $this->assertSame(13000, (new Decimal(100))->addPercent(30)->getCents());
        $this->assertSame(9375, (new Decimal(75))->addPercent(25)->getCents());
        $this->assertSame(1313, (new Decimal(10.50))->addPercent(25)->getCents());
        $this->assertSame(7500, (new Decimal(75))->addPercent(0)->getCents());
    }

    /**
     * Test subtracting a percentage
     *
     */
    public function testSubtractPercent()
    {
        $this->assertSame(9000, (new Decimal(100))->subtractPercent(10)->getCents());
        $this->assertSame(7000, (new Decimal(100))->subtractPercent(30)->getCents());
        $this->assertSame(5625, (new Decimal(75))->subtractPercent(25)->getCents());
        $this->assertSame(787, (new Decimal(10.50))->subtractPercent(25)->getCents());
        $this->assertSame(7500, (new Decimal(75))->subtractPercent(0)->getCents());
    }

    /**
     * Serialising an object should return a string value
     *
     */
    public function testSerialization()
    {
        $decimal = new Decimal(8.88);
        $this->assertSame('"8.88"', json_encode($decimal));
    }
}
