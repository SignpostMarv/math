<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Calculator;

use SignpostMarv\Brick\Math\Calculator;

/**
 * Calculator implementation built around the GMP library.
 */
class GmpCalculator extends Calculator
{
    /**
     * {@inheritdoc}
     */
    public function add(string $a, string $b) : string
    {
        return \gmp_strval(\gmp_add($a, $b));
    }

    /**
     * {@inheritdoc}
     */
    public function sub(string $a, string $b) : string
    {
        return \gmp_strval(\gmp_sub($a, $b));
    }

    /**
     * {@inheritdoc}
     */
    public function mul(string $a, string $b) : string
    {
        return \gmp_strval(\gmp_mul($a, $b));
    }

    /**
     * {@inheritdoc}
     */
    public function divQR(string $a, string $b) : array
    {
        [$q, $r] = \gmp_div_qr($a, $b);

        return [
            \gmp_strval($q),
            \gmp_strval($r)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromBase(string $number, int $base) : string
    {
        return \gmp_strval(\gmp_init($number, $base));
    }

    /**
     * {@inheritdoc}
     */
    public function toBase(string $number, int $base) : string
    {
        return \gmp_strval($number, $base);
    }
}
