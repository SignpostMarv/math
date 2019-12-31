<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\BcMathCalculator;

/**
 * Unit tests for class BcMathCalculator.
 */
class BcMathCalculatorTest extends AbstractCalculatorTest
{
	protected function ObtainCalculator() : Calculator
	{
		return new BcMathCalculator();
	}
}
