<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Calculator;

use function assert;
use function intdiv;
use function is_int;
use function is_null;
use function ltrim;
use const PHP_INT_SIZE;
use RuntimeException;
use SignpostMarv\Brick\Math\Calculator;
use function str_pad;
use const STR_PAD_LEFT;
use function str_repeat;
use function strcmp;
use function strlen;
use function substr;

/**
 * Calculator implementation using only native PHP code.
 */
class NativeCalculator extends Calculator
{
	public const MAX_DIGITS = [
		4 => 9,
		8 => 18,
	];

	public const COMPARE_LESS_THAN = -1;

	public const SUBSTR_FROM_START = 0;

	public const BREAK_AT_ZERO = 0;

	public const SPACESHIP_ZERO = 0;

	public const RESET_UNDER_ZERO = 0;

	public const CARRY_ZERO = 0;

	public const DIVIDE_BY_TWO = 2;

	/**
	 * The max number of digits the platform can natively add, subtract, multiply or divide without overflow.
	 * For multiplication, this represents the max sum of the lengths of both operands.
	 *
	 * For addition, it is assumed that an extra digit can hold a carry (1) without overflowing.
	 * Example: 32-bit: max number 1,999,999,999 (9 digits + carry)
	 *          64-bit: max number 1,999,999,999,999,999,999 (18 digits + carry)
	 */
	private int $maxDigits;

	/**
	 * Class constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @throws RuntimeException if the platform is unsupported
	 */
	public function __construct()
	{
		$maxDigits = self::MAX_DIGITS[PHP_INT_SIZE] ?? null;

		if ( ! is_int($maxDigits)) {
			throw new RuntimeException('The platform is not 32-bit or 64-bit as expected.');
		}

		$this->maxDigits = $maxDigits;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(string $a, string $b) : string
	{
		/** @var numeric */
		$a = $a;

		/** @var numeric */
		$b = $b;

		/**
		 * @var int|float
		 */
		$result = $a + $b;

		if (is_int($result)) {
			return (string) $result;
		}

		/** @var string */
		$a = $a;

		/** @var string */
		$b = $b;

		if ('0' === $a) {
			return $b;
		}

		if ('0' === $b) {
			return $a;
		}

		[$aDig, $bDig, $aNeg, $bNeg] = $this->init($a, $b);

		if ($aNeg === $bNeg) {
			$result = $this->doAdd($aDig, $bDig);
		} else {
			$result = $this->doSub($aDig, $bDig);
		}

		if ($aNeg) {
			$result = $this->neg($result);
		}

		/**
		 * @var string
		 */
		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mul(string $a, string $b) : string
	{
		if ('0' === $a || '0' === $b) {
			return '0';
		}

		if ('1' === $a) {
			return $b;
		}

		if ('1' === $b) {
			return $a;
		}

		if ('-1' === $a) {
			return $this->neg($b);
		}

		if ('-1' === $b) {
			return $this->neg($a);
		}

		/** @var numeric */
		$a = $a;

		/** @var numeric */
		$b = $b;

		/**
		 * @var int|float
		 */
		$result = $a * $b;

		if (is_int($result)) {
			return (string) $result;
		}

		/** @var string */
		$a = $a;

		/** @var string */
		$b = $b;

		[$aDig, $bDig, $aNeg, $bNeg] = $this->init($a, $b);

		$result = $this->doMul($aDig, $bDig);

		if ($aNeg !== $bNeg) {
			$result = $this->neg($result);
		}

		/**
		 * @var string
		 */
		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function divQR(string $a, string $b) : array
	{
		$maybe = $this->MaybeEarlyExitDivQR($a, $b);

		if ( ! is_null($maybe)) {
			return $maybe;
		}

		/** @var numeric */
		$a = $a;

		/** @var numeric */
		$b = $b;

		$na = $a * 1; // cast to number

		if (is_int($na)) {
			$nb = $b * 1;

			if (is_int($nb)) {
				// the only division that may overflow is PHP_INT_MIN / -1,
				// which cannot happen here as we've already handled a divisor of -1 above.
				$r = $na % $nb;
				$q = ($na - $r) / $nb;

				assert(is_int($q));

				return [
					(string) $q,
					(string) $r,
				];
			}
		}

		/** @var string */
		$a = $a;

		/** @var string */
		$b = $b;

		[$aDig, $bDig, $aNeg, $bNeg] = $this->init($a, $b);

		/** @var array{0:string, 1:string} */
		$done_div = $this->doDiv($aDig, $bDig);

		[$q, $r] = $done_div;

		if ($aNeg !== $bNeg) {
			$q = $this->neg($q);
		}

		if ($aNeg) {
			$r = $this->neg($r);
		}

		return [$q, $r];
	}

	/**
	 * Subtracts two numbers.
	 *
	 * @param string $a the minuend
	 * @param string $b the subtrahend
	 *
	 * @return string the difference
	 */
	private function sub(string $a, string $b) : string
	{
		return $this->add($a, $this->neg($b));
	}

	/**
	 * Performs the addition of two non-signed large integers.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 */
	private function doAdd(string $a, string $b) : string
	{
		$length = $this->pad($a, $b);

		$carry = 0;
		$result = '';
		$i = $length - $this->maxDigits;

		for (;; $i -= $this->maxDigits) {
			$blockLength = $this->maxDigits;

			if ($i < self::RESET_UNDER_ZERO) {
				$blockLength += $i;
				$i = self::RESET_UNDER_ZERO;
			}

			/**
			 * @var numeric
			 */
			$blockA = substr($a, $i, $blockLength);

			/**
			 * @var numeric
			 */
			$blockB = substr($b, $i, $blockLength);

			$sum = (string) ($blockA + $blockB + $carry);
			$sumLength = strlen($sum);

			if ($sumLength > $blockLength) {
				$sum = substr($sum, 1);
				$carry = 1;
			} else {
				if ($sumLength < $blockLength) {
					$sum = str_repeat('0', $blockLength - $sumLength) . $sum;
				}
				$carry = 0;
			}

			$result = $sum . $result;

			if (0 === $i) {
				break;
			}
		}

		if (1 === $carry) {
			$result = '1' . $result;
		}

		return $result;
	}

	/**
	 * Performs the subtraction of two non-signed large integers.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 */
	private function doSub(string $a, string $b) : string
	{
		if ($a === $b) {
			return '0';
		}

		// Ensure that we always subtract to a positive result: biggest minus smallest.
		$cmp = $this->doCmp($a, $b);

		$invert = (self::COMPARE_LESS_THAN === $cmp);

		if ($invert) {
			$c = $a;
			$a = $b;
			$b = $c;
		}

		$length = $this->pad($a, $b);

		$carry = 0;
		$result = '';

		$complement = 10 ** $this->maxDigits;
		$i = $length - $this->maxDigits;

		for (;; $i -= $this->maxDigits) {
			$blockLength = $this->maxDigits;

			if ($i < self::RESET_UNDER_ZERO) {
				$blockLength += $i;
				$i = self::RESET_UNDER_ZERO;
			}

			/**
			 * @var numeric
			 */
			$blockA = substr($a, $i, $blockLength);

			/**
			 * @var numeric
			 */
			$blockB = substr($b, $i, $blockLength);

			$sum = $blockA - $blockB - $carry;

			if ($sum < 0) {
				$sum += $complement;
				$carry = 1;
			} else {
				$carry = 0;
			}

			$sum = (string) $sum;
			$sumLength = strlen($sum);

			if ($sumLength < $blockLength) {
				$sum = str_repeat('0', $blockLength - $sumLength) . $sum;
			}

			$result = $sum . $result;

			if (0 === $i) {
				break;
			}
		}

		// Carry cannot be 1 when the loop ends, as a > b
		assert(0 === $carry);

		$result = ltrim($result, '0');

		if ($invert) {
			$result = $this->neg($result);
		}

		/**
		 * @var string
		 */
		return $result;
	}

	/**
	 * Performs the multiplication of two non-signed large integers.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 */
	private function doMul(string $a, string $b) : string
	{
		$x = strlen($a);
		$y = strlen($b);

		$maxDigits = intdiv($this->maxDigits, self::DIVIDE_BY_TWO);
		$complement = 10 ** $maxDigits;

		$result = '0';
		$i = $x - $maxDigits;

		for (;; $i -= $maxDigits) {
			$blockALength = $maxDigits;

			if ($i < self::RESET_UNDER_ZERO) {
				$blockALength += $i;
				$i = self::RESET_UNDER_ZERO;
			}

			$blockA = (int) substr($a, $i, $blockALength);

			$line = '';
			$carry = 0;
			$j = $y - $maxDigits;

			for (;; $j -= $maxDigits) {
				$blockBLength = $maxDigits;

				if ($j < self::RESET_UNDER_ZERO) {
					$blockBLength += $j;
					$j = self::RESET_UNDER_ZERO;
				}

				$blockB = (int) substr($b, $j, $blockBLength);

				$mul = $blockA * $blockB + $carry;
				$value = $mul % $complement;
				$carry = (int) (($mul - $value) / $complement);

				$value = (string) $value;
				$value = str_pad($value, $maxDigits, '0', STR_PAD_LEFT);

				$line = $value . $line;

				if (self::BREAK_AT_ZERO === $j) {
					break;
				}
			}

			if (self::CARRY_ZERO !== $carry) {
				$line = (string) $carry . (string) $line;
			}

			$line = ltrim($line, '0');

			if ('' !== $line) {
				$line .= str_repeat('0', $x - $blockALength - $i);
				$result = $this->add($result, $line);
			}

			if (self::BREAK_AT_ZERO === $i) {
				break;
			}
		}

		/**
		 * @var string
		 */
		return $result;
	}

	/**
	 * Performs the division of two non-signed large integers.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 *
	 * @return array{0:numeric, 1:numeric} The quotient and remainder
	 */
	private function doDiv(string $a, string $b) : array
	{
		$cmp = $this->doCmp($a, $b);

		if (self::COMPARE_LESS_THAN === $cmp) {
			return ['0', $a];
		}

		$x = strlen($a);
		$y = strlen($b);

		// we now know that a >= b && x >= y

		$q = '0'; // quotient
		$r = $a; // remainder
		$z = $y; // focus length, always $y or $y+1

		  while (true){
			$focus = substr($a, self::SUBSTR_FROM_START, $z);

			$cmp = $this->doCmp($focus, $b);

			if (self::COMPARE_LESS_THAN === $cmp) {
				if ($z === $x) { // remainder < dividend
					break;
				}

				++$z;
			}

			$zeros = str_repeat('0', $x - $z);

			$q = $this->add($q, '1' . $zeros);
			$a = $this->sub($a, $b . $zeros);

			/**
			 * @var numeric|'0'
			 */
			$r = $a;

			if ('0' === $r) { // remainder == 0
				break;
			}

			$x = strlen($a);

			if ($x < $y) { // remainder < dividend
				break;
			}

			$z = $y;
		}

		return [$q, $r];
	}

	/**
	 * Compares two non-signed large numbers.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 *
	 * @return int [-1, 0, 1]
	 */
	private function doCmp(string $a, string $b) : int
	{
		$x = strlen($a);
		$y = strlen($b);

		$cmp = $x <=> $y;

		if (0 !== $cmp) {
			return $cmp;
		}

		return strcmp($a, $b) <=> self::SPACESHIP_ZERO; // enforce [-1, 0, 1]
	}

	/**
	 * Pads the left of one of the given numbers with zeros if necessary to make both numbers the same length.
	 *
	 * The numbers must only consist of digits, without leading minus sign.
	 *
	 * @param string $a the first operand
	 * @param string $b the second operand
	 *
	 * @return int the length of both strings
	 */
	private function pad(string &$a, string &$b) : int
	{
		$x = strlen($a);
		$y = strlen($b);

		if ($x > $y) {
			$b = str_repeat('0', $x - $y) . $b;

			return $x;
		}

		if ($x < $y) {
			$a = str_repeat('0', $y - $x) . $a;

			return $y;
		}

		return $x;
	}
}
