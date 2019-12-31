<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests;

use SignpostMarv\Brick\Math\Calculator;

use PHPUnit\Framework\TestCase;

/**
 * Tests for Calculator implementation detection.
 */
class CalculatorDetectTest extends TestCase
{
    public function testGetWithNoCalculatorSetDetectsCalculator() : void
    {
        $currentCalculator = Calculator::get();

        Calculator::set(null);
        $this->assertInstanceOf(Calculator::class, Calculator::get());

        Calculator::set($currentCalculator);
    }
}
