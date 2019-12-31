<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math;

use function dechex;
use function ltrim;
use function ord;
use function preg_match;
use function strlen;
use function strtoupper;
use function trim;
use InvalidArgumentException;

/**
 * Performs basic operations on arbitrary size integers.
 *
 * Unless otherwise specified, all parameters must be validated as non-empty strings of digits,
 * without leading zero, and with an optional leading minus sign if the number is not zero.
 *
 * Any other parameter format will lead to undefined behaviour.
 * All methods must return strings respecting this format, unless specified otherwise.
 */
abstract class Calculator
{
    /**
     * The alphabet for converting from and to base 2 to 36, lowercase.
     */
    public const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * The Calculator instance in use.
     */
    private static ? Calculator $instance = null;

    /**
     * Sets the Calculator instance to use.
     *
     * An instance is typically set only in unit tests: the autodetect is usually the best option.
     *
     * @param Calculator|null $calculator The calculator instance, or NULL to revert to autodetect.
     *
     * @return void
     */
    final public static function set(?Calculator $calculator) : void
    {
        self::$instance = $calculator;
    }

    /**
     * Returns the Calculator instance to use.
     *
     * If none has been explicitly set, the fastest available implementation will be returned.
     *
     * @return Calculator
     */
    final public static function get() : Calculator
    {
        if (self::$instance === null) {
            self::$instance = self::detect();
        }

        return self::$instance;
    }

    /**
     * Returns the fastest available Calculator implementation.
     *
     * @codeCoverageIgnore
     *
     * @return Calculator
     */
    private static function detect() : Calculator
    {
        if (\extension_loaded('gmp')) {
            return new Calculator\GmpCalculator();
        }

        if (\extension_loaded('bcmath')) {
            return new Calculator\BcMathCalculator();
        }

        return new Calculator\NativeCalculator();
    }

    /**
     * Extracts the digits and sign of the operands.
     *
     * @param string $a    The first operand.
     * @param string $b    The second operand.
     *
     * @return array{0:string, 1:string, 2:bool, 3:bool} the digits of the first operand, the digits of the second operand, whether the first operand is negative, whether the second operand is negative.
     */
    final protected function init(string $a, string $b) : array
    {
        $aNeg = ($a[0] === '-');
        $bNeg = ($b[0] === '-');

        $aDig = $aNeg ? \substr($a, 1) : $a;
        $bDig = $bNeg ? \substr($b, 1) : $b;

        return [
            $aDig,
            $bDig,
            $aNeg,
            $bNeg,
        ];
    }

    /**
     * Negates a number.
     *
     * @param string $n The number.
     *
     * @return string The negated value.
     */
    final public function neg(string $n) : string
    {
        if ($n === '0') {
            return '0';
        }

        if ($n[0] === '-') {
            return \substr($n, 1);
        }

        return '-' . $n;
    }

    /**
     * Adds two numbers.
     *
     * @param string $a The augend.
     * @param string $b The addend.
     *
     * @return string The sum.
     */
    abstract public function add(string $a, string $b) : string;

    /**
     * Subtracts two numbers.
     *
     * @param string $a The minuend.
     * @param string $b The subtrahend.
     *
     * @return string The difference.
     */
    abstract public function sub(string $a, string $b) : string;

    /**
     * Multiplies two numbers.
     *
     * @param string $a The multiplicand.
     * @param string $b The multiplier.
     *
     * @return string The product.
     */
    abstract public function mul(string $a, string $b) : string;

    /**
     * Returns the quotient and remainder of the division of two numbers.
     *
     * @param string $a The dividend.
     * @param string $b The divisor, must not be zero.
     *
     * @return array{0:string, 1:string} An array containing the quotient and remainder.
     */
    abstract public function divQR(string $a, string $b) : array;

    /**
     * Converts a number from an arbitrary base.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for base conversion.
     *
     * @param string $number The number, positive or zero, non-empty, case-insensitively validated for the given base.
     * @param int    $base   The base of the number, validated from 2 to 36.
     *
     * @return string The converted number, following the Calculator conventions.
     */
    public function fromBase(string $number, int $base) : string
    {
        return $this->fromArbitraryBase(\strtolower($number), self::ALPHABET, $base);
    }

    /**
     * Converts a number to an arbitrary base.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for base conversion.
     *
     * @param string $number The number to convert, following the Calculator conventions.
     * @param int    $base   The base to convert to, validated from 2 to 36.
     *
     * @return string The converted number, lowercase.
     */
    public function toBase(string $number, int $base) : string
    {
        if ($base < 2 || $base > 36) {
            throw new InvalidArgumentException(
                'Argument 2 must be between 2 and 36'
            );
        }

        $negative = ($number[0] === '-');

        if ($negative) {
            $number = \substr($number, 1);
        }

        $number = $this->toArbitraryBase($number, self::ALPHABET, $base);

        if ($negative) {
            return '-' . $number;
        }

        return $number;
    }

    /**
     * Converts a non-negative number in an arbitrary base using a custom alphabet, to base 10.
     *
     * @param string $number   The number to convert, validated as a non-empty string,
     *                         containing only chars in the given alphabet/base.
     * @param string $alphabet The alphabet that contains every digit, validated as 2 chars minimum.
     * @param int    $base     The base of the number, validated from 2 to alphabet length.
     *
     * @return string The number in base 10, following the Calculator conventions.
     */
    final public function fromArbitraryBase(string $number, string $alphabet, int $base) : string
    {
        $this->ValidateAlphabet($number, $alphabet);

        // remove leading "zeros"
        $number = ltrim($number, $alphabet[0]);

        if ($number === '') {
            return '0';
        }

        // optimize for "one"
        if ($number === $alphabet[1]) {
            return '1';
        }

        $result = '0';
        $power = '1';

        $base = (string) $base;

        for ($i = \strlen($number) - 1; $i >= 0; $i--) {
            $index = \strpos($alphabet, $number[$i]);

            if ($index !== 0) {
                $result = $this->add($result, ($index === 1)
                    ? $power
                    : $this->mul($power, (string) $index)
                );
            }

            if ($i !== 0) {
                $power = $this->mul($power, $base);
            }
        }

        return $result;
    }

    /**
     * Converts a non-negative number to an arbitrary base using a custom alphabet.
     *
     * @param string $number   The number to convert, positive or zero, following the Calculator conventions.
     * @param string $alphabet The alphabet that contains every digit, validated as 2 chars minimum.
     * @param int    $base     The base to convert to, validated from 2 to alphabet length.
     *
     * @return string The converted number in the given alphabet.
     */
    final public function toArbitraryBase(string $number, string $alphabet, int $base) : string
    {
        $number = trim($number);

        if ('-' === ($number[0] ?? '')) {
            throw new InvalidArgumentException(
                'toArbitraryBase() does not support negative numbers.'
            );
        }

        $this->ValidateAlphabet($alphabet[0] ?? '0', $alphabet);

        if ($number === '0') {
            return $alphabet[0];
        }

        $base = (string) $base;
        $result = '';

        while ($number !== '0') {
            [$number, $remainder] = $this->divQR($number, $base);
            $remainder = (int) $remainder;

            $result .= $alphabet[$remainder];
        }

        return \strrev($result);
    }

    protected function ValidateAlphabet(string $number, string $alphabet) : void
    {
        if ('' === $number) {
            throw new InvalidArgumentException(
                'The number cannot be empty.'
            );
        }

        if (strlen($alphabet) < 2) {
            throw new InvalidArgumentException(
                'The alphabet must contain at least 2 chars.'
            );
        }

        $pattern = '/[^' . \preg_quote($alphabet, '/') . ']/';

        if (preg_match($pattern, $number, $matches) === 1) {
            $char = $matches[0];

            $ord = ord($char);

            if ($ord < 32 || $ord > 126) {
                $char = strtoupper(dechex($ord));

                if ($ord < 10) {
                    $char = '0' . $char;
                }
            } else {
                $char = '"' . $char . '"';
            }

            throw new InvalidArgumentException(sprintf(
                'Char %s is not a valid character in the given alphabet.',
                $char
            ));
        }
    }
}
