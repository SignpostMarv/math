<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests;

use PHPUnit\Framework\TestCase;
use SignpostMarv\Brick\Math\Calculator;

/**
 * Base class for math tests.
 */
abstract class AbstractTestCase extends TestCase
{
    abstract protected function ObtainCalculator() : Calculator;
}
