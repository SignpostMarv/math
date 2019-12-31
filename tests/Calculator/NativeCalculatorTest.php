<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use SignpostMarv\Brick\Math\Calculator\NativeCalculator;
use SignpostMarv\Brick\Math\Tests\AbstractTestCase;

/**
 * Unit tests for class NativeCalculator.
 */
class NativeCalculatorTest extends AbstractTestCase
{
    /**
     * @dataProvider providerAdd
     */
    public function testAdd(string $a, string $b, string $expectedValue) : void
    {
        $nativeCalculator = new NativeCalculator();
        $this->assertSame($expectedValue, $nativeCalculator->add($a, $b));
    }

    /**
     * @return list<array{0:string, 1:string, 2:string}>
     */
    public function providerAdd() : array
    {
        return [
            ['0', '1234567891234567889999999', '1234567891234567889999999'],
            ['1234567891234567889999999', '0', '1234567891234567889999999'],

            ['1234567891234567889999999', '-1234567891234567889999999', '0'],
            ['-1234567891234567889999999', '1234567891234567889999999', '0'],

            ['1234567891234567889999999', '1234567891234567889999999', '2469135782469135779999998'],
        ];
    }

    /**
     * @dataProvider providerMul
     */
    public function testMul(string $a, string $b, string $expectedValue) : void
    {
        $nativeCalculator = new NativeCalculator();
        $this->assertSame($expectedValue, $nativeCalculator->mul($a, $b));
    }

    /**
     * @return list<array{0:string, 1:string, 2:string}>
     */
    public function providerMul() : array
    {
        return [
            ['0', '0', '0'],

            ['1', '1234567891234567889999999', '1234567891234567889999999'],
            ['1234567891234567889999999', '1', '1234567891234567889999999'],

            ['1234567891234567889999999', '-1234567891234567889999999', '-1524157878067367851562259605883269630864220000001'],
            ['-1234567891234567889999999', '1234567891234567889999999', '-1524157878067367851562259605883269630864220000001'],

            ['1234567891234567889999999', '1234567891234567889999999', '1524157878067367851562259605883269630864220000001'],
        ];
    }
}
