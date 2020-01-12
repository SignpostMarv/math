<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math;

use function dechex;
use InvalidArgumentException;
use function ltrim;
use function ord;
use function preg_match;
use function trim;

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

	const BASE2 = 2;

	const BASE36 = 36;

	const ORD10 = 10;

	const ORD32 = 32;

	const ORD126 = 126;

	/**
	 * Negates a number.
	 *
	 * @param string $n the number
	 *
	 * @return string the negated value
	 */
	final public function neg(string $n) : string
	{
		if ('0' === $n) {
			return '0';
		}

		if ('-' === $n[0]) {
			return \substr($n, 1);
		}

		return '-' . $n;
	}

	/**
	 * Adds two numbers.
	 *
	 * @param string $a the augend
	 * @param string $b the addend
	 *
	 * @return string the sum
	 */
	abstract public function add(string $a, string $b) : string;

	/**
	 * Multiplies two numbers.
	 *
	 * @param string $a the multiplicand
	 * @param string $b the multiplier
	 *
	 * @return string the product
	 */
	abstract public function mul(string $a, string $b) : string;

	/**
	 * Returns the quotient and remainder of the division of two numbers.
	 *
	 * @param string $a the dividend
	 * @param string $b the divisor, must not be zero
	 *
	 * @return array{0:string, 1:string} An array containing the quotient and remainder
	 */
	abstract public function divQR(string $a, string $b) : array;

	/**
	 * Converts a number from an arbitrary base.
	 *
	 * This method can be overridden by the concrete implementation if the underlying library
	 * has built-in support for base conversion.
	 *
	 * @param string $number the number, positive or zero, non-empty, case-insensitively validated for the given base
	 * @param int    $base   the base of the number, validated from 2 to 36
	 *
	 * @throws InvalidArgumentException if $number is invalid
	 *
	 * @return string the converted number, following the Calculator conventions
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
	 * @param string $number the number to convert, following the Calculator conventions
	 * @param int    $base   the base to convert to, validated from 2 to 36
	 *
	 * @throws InvalidArgumentException if $base is unsupported
	 *
	 * @return string the converted number, lowercase
	 */
	public function toBase(string $number, int $base) : string
	{
		if ($base < self::BASE2 || $base > self::BASE36) {
			throw new InvalidArgumentException(
				'Argument 2 must be between 2 and 36'
			);
		}

		$negative = ('-' === $number[0]);

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
	 * @param string $number   the number to convert, validated as a non-empty string,
	 *                         containing only chars in the given alphabet/base
	 * @param string $alphabet the alphabet that contains every digit, validated as 2 chars minimum
	 * @param int    $base     the base of the number, validated from 2 to alphabet length
	 *
	 * @throws InvalidArgumentException if $number or $alphabet are invalid
	 *
	 * @return string the number in base 10, following the Calculator conventions
	 */
	final public function fromArbitraryBase(string $number, string $alphabet, int $base) : string
	{
		$this->ValidateAlphabet($number, $alphabet);

		// remove leading "zeros"
		$number = ltrim($number, $alphabet[0]);

		if ('' === $number) {
			return '0';
		}

		// optimize for "one"
		if ($number === $alphabet[1]) {
			return '1';
		}

		$result = '0';
		$power = '1';

		$base = (string) $base;

		for ($i = \strlen($number) - 1; $i >= 0; --$i) {
			$index = \strpos($alphabet, $number[$i]);

			if (0 !== $index) {
				$result = $this->add($result, (1 === $index)
					? $power
					: $this->mul($power, (string) $index)
				);
			}

			if (0 !== $i) {
				$power = $this->mul($power, $base);
			}
		}

		return $result;
	}

	/**
	 * Converts a non-negative number to an arbitrary base using a custom alphabet.
	 *
	 * @param string $number   the number to convert, positive or zero, following the Calculator conventions
	 * @param string $alphabet the alphabet that contains every digit, validated as 2 chars minimum
	 * @param int    $base     the base to convert to, validated from 2 to alphabet length
	 *
	 * @throws InvalidArgumentException if $number represents a negative number
	 *
	 * @return string the converted number in the given alphabet
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

		if ('0' === $number) {
			return $alphabet[0];
		}

		$base = (string) $base;
		$result = '';

		while ('0' !== $number) {
			[$number, $remainder] = $this->divQR($number, $base);
			$remainder = (int) $remainder;

			$result .= $alphabet[$remainder];
		}

		return \strrev($result);
	}

	/**
	 * Extracts the digits and sign of the operands.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 *
	 * @return array{0:string, 1:string, 2:bool, 3:bool} the digits of the first operand, the digits of the second operand, whether the first operand is negative, whether the second operand is negative
	 */
	final protected function init(string $a, string $b) : array
	{
		$aNeg = ('-' === $a[0]);
		$bNeg = ('-' === $b[0]);

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
	* @throws InvalidArgumentException if $number is an empty string
	* @throws InvalidArgumentException if $alphabet does not contain sufficient characters
	* @throws InvalidArgumentException if $number cannot exist in $alphabet
	*/
	protected function ValidateAlphabet(string $number, string $alphabet) : void
	{
		if ('' === $number) {
			throw new InvalidArgumentException(
				'The number cannot be empty.'
			);
		}

		if (strlen($alphabet) < self::BASE2) {
			throw new InvalidArgumentException(
				'The alphabet must contain at least 2 chars.'
			);
		}

		$pattern = '/[^' . \preg_quote($alphabet, '/') . ']/';

		if (1 === preg_match($pattern, $number, $matches)) {
			$char = $matches[0];

			$ord = ord($char);

			if ($ord < self::ORD32 || $ord > self::ORD126) {
				$char = strtoupper(dechex($ord));

				if ($ord < self::ORD10) {
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

	/**
	* Runs some checks for early exits of Calculator::divQR().
	*
	* @return array{0:string, 1:string}|null returns null if not exiting early
	*/
	protected function MaybeEarlyExitDivQR(string $a, string $b) : ? array
	{
		if ('0' === $a) {
			return ['0', '0'];
		}

		if ($a === $b) {
			return ['1', '0'];
		}

		if ('1' === $b) {
			return [$a, '0'];
		}

		if ('-1' === $b) {
			return [$this->neg($a), '0'];
		}

		return null;
	}
}
