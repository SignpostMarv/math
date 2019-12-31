<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\NativeCalculator;

/**
 * Unit tests for class NativeCalculator.
 */
class NativeCalculatorTest extends AbstractCalculatorTest
{
	protected function ObtainCalculator() : Calculator
	{
		return new NativeCalculator();
	}
}
