<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use RuntimeException;
use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\NativeCalculator;

/**
 * Unit tests for class NativeCalculator.
 */
class NativeCalculatorTest extends AbstractCalculatorTest
{
	/**
	 * @throws RuntimeException if NativeCalculator::__construct() does not support the current platform
	 */
	protected function ObtainCalculator() : Calculator
	{
		return new NativeCalculator();
	}
}
