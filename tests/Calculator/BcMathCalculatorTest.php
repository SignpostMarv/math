<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use Generator;
use InvalidArgumentException;
use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\BcMathCalculator;
use SignpostMarv\Brick\Math\Tests\AbstractTestCase;

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
