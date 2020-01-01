<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests\Calculator;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SignpostMarv\Brick\Math\Calculator;
use SignpostMarv\Brick\Math\Calculator\NativeCalculator;

/**
 * Unit tests for class NativeCalculator.
 */
abstract class AbstractCalculatorTest extends TestCase
{
	/**
	 * @dataProvider providerAdd
	 */
	public function testAdd(string $a, string $b, string $expectedValue) : void
	{
		$nativeCalculator = $this->ObtainCalculator();
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
		$nativeCalculator = $this->ObtainCalculator();
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

			['-1', '1234567891234567889999999', '-1234567891234567889999999'],
			['1234567891234567889999999', '-1', '-1234567891234567889999999'],

			['1234567891234567889999999', '-1234567891234567889999999', '-1524157878067367851562259605883269630864220000001'],
			['-1234567891234567889999999', '1234567891234567889999999', '-1524157878067367851562259605883269630864220000001'],

			['1234567891234567889999999', '1234567891234567889999999', '1524157878067367851562259605883269630864220000001'],
		];
	}

	/**
	 * @dataProvider providerToBase
	 *
	 * @param int|float|string $number   the number to convert, in base 10
	 * @param int              $base     the base to convert the number to
	 * @param string           $expected the expected result
	 */
	public function testToBase($number, int $base, string $expected) : void
	{
		$this->assertSame($expected, $this->ObtainCalculator()->toBase((string) $number, $base));
	}

	/**
	 * @return Generator<int, array{0:int|float|string, 1:int, 2:string}>
	 */
	public function providerToBase() : Generator
	{
		$tests = [
			['640998479760579495168036691627608949', 36, '110011001100110011001111'],
			['335582856048758779730579523833856636', 35, '110011001100110011001111'],
			['172426711023004493064981145981549295', 34, '110011001100110011001111'],
			['86853227285668653965326574185738990',  33, '110011001100110011001111'],
			['42836489934972583913564073319498785',  32, '110011001100110011001111'],
			['20658924711984480538771889603666144',  31, '110011001100110011001111'],
			['9728140488839986222205212599027931',   30, '110011001100110011001111'],
			['4465579470019956787945275674107410',   29, '110011001100110011001111'],
			['1994689924537781753408144504465645',   28, '110011001100110011001111'],
			['865289950909412968716094193925700',    27, '110011001100110011001111'],
			['363729369583879309352831568000039',    26, '110011001100110011001111'],
			['147793267388865354156500488297526',    25, '110011001100110011001111'],
			['57888012016107577099138793486425',     24, '110011001100110011001111'],
			['21788392294523974761749372677800',     23, '110011001100110011001111'],
			['7852874701996329566765721637715',      22, '110011001100110011001111'],
			['2699289081943123258094476428634',      21, '110011001100110011001111'],
			['880809345058406615041344008421',       20, '110011001100110011001111'],
			['271401690926468032718781859340',       19, '110011001100110011001111'],
			['78478889737009209699633503455',        18, '110011001100110011001111'],
			['21142384915931646646976872830',        17, '110011001100110011001111'],
			['5261325448418072742917574929',         16, '110011001100110011001111'],
			['1197116069565850925807253616',         15, '110011001100110011001111'],
			['245991074299834917455374155',          14, '110011001100110011001111'],
			['44967318722190498361960610',           13, '110011001100110011001111'],
			['7177144825886069940574045',            12, '110011001100110011001111'],
			['976899716207148313491924',             11, '110011001100110011001111'],
			['110011001100110011001111',             10, '110011001100110011001111'],
			['9849210196991880028870',                9, '110011001100110011001111'],
			['664244955832213832265',                 8, '110011001100110011001111'],
			['31291601125492514360',                  7, '110011001100110011001111'],
			['922063395565287619',                    6, '110011001100110011001111'],
			['14328039609468906',                     5, '110011001100110011001111'],
			['88305875046485',                        4, '110011001100110011001111'],
			['127093291420',                          3, '110011001100110011001111'],
			['13421775',                              2, '110011001100110011001111'],

			['106300512100105327644605138221229898724869759421181854980', 36, 'zyxwvutsrqponmlkjihgfedcba9876543210'],
			['1101553773143634726491620528194292510495517905608180485',   35,  'yxwvutsrqponmlkjihgfedcba9876543210'],
			['11745843093701610854378775891116314824081102660800418',     34,   'xwvutsrqponmlkjihgfedcba9876543210'],
			['128983956064237823710866404905431464703849549412368',       33,    'wvutsrqponmlkjihgfedcba9876543210'],
			['1459980823972598128486511383358617792788444579872',         32,     'vutsrqponmlkjihgfedcba9876543210'],
			['17050208381689099029767742314582582184093573615',           31,      'utsrqponmlkjihgfedcba9876543210'],
			['205646315052919334126040428061831153388822830',             30,       'tsrqponmlkjihgfedcba9876543210'],
			['2564411043271974895869785066497940850811934',               29,        'srqponmlkjihgfedcba9876543210'],
			['33100056003358651440264672384704297711484',                 28,         'rqponmlkjihgfedcba9876543210'],
			['442770531899482980347734468443677777577',                   27,          'qponmlkjihgfedcba9876543210'],
			['6146269788878825859099399609538763450',                     26,           'ponmlkjihgfedcba9876543210'],
			['88663644327703473714387251271141900',                       25,            'onmlkjihgfedcba9876543210'],
			['1331214537196502869015340298036888',                        24,             'nmlkjihgfedcba9876543210'],
			['20837326537038308910317109288851',                          23,              'mlkjihgfedcba9876543210'],
			['340653664490377789692799452102',                            22,               'lkjihgfedcba9876543210'],
			['5827980550840017565077671610',                              21,                'kjihgfedcba9876543210'],
			['104567135734072022160664820',                               20,                 'jihgfedcba9876543210'],
			['1972313422155189164466189',                                 19,                  'ihgfedcba9876543210'],
			['39210261334551566857170',                                   18,                   'hgfedcba9876543210'],
			['824008854613343261192',                                     17,                    'gfedcba9876543210'],
			['18364758544493064720',                                      16,                     'fedcba9876543210'],
			['435659737878916215',                                        15,                      'edcba9876543210'],
			['11046255305880158',                                         14,                       'dcba9876543210'],
			['300771807240918',                                           13,                        'cba9876543210'],
			['8842413667692',                                             12,                         'ba9876543210'],
			['282458553905',                                              11,                          'a9876543210'],
			['9876543210',                                                10,                           '9876543210'],
			['381367044',                                                  9,                            '876543210'],
			['16434824',                                                   8,                             '76543210'],
			['800667',                                                     7,                              '6543210'],
			['44790',                                                      6,                               '543210'],
			['2930',                                                       5,                                '43210'],
			['228',                                                        4,                                 '3210'],
			['21',                                                         3,                                  '210'],
			['2',                                                          2,                                   '10'],

			['1', 2, '1'],
			['0', 2, '0'],

			['1', 8, '1'],
			['0', 8, '0'],
		];

		foreach ($tests as [$number, $base, $expected]) {
			yield [$number, $base, $expected];

			if ('0' !== $number[0]) {
				yield ['-' . $number, $base, '-' . $expected];
			}
		}
	}

	/**
	 * @dataProvider providerToInvalidBaseThrowsException
	 */
	public function testToInvalidBaseThrowsException(int $base) : void
	{
		static::expectException(InvalidArgumentException::class);

		$this->ObtainCalculator()->toBase('0', $base);
	}

	/**
	 * @return list<array{0:int}>
	 */
	public function providerToInvalidBaseThrowsException() : array
	{
		return [
			[-2],
			[-1],
			[0],
			[1],
			[37],
		];
	}

	/**
	 * @dataProvider providerFromArbitraryBase
	 *
	 * @param string $base10
	 */
	public function testFromArbitraryBase($base10, string $alphabet, string $baseN) : void
	{
		if (Calculator::ALPHABET === $alphabet) {
			$number = $this->ObtainCalculator()->fromBase($baseN, 36);
		} else {
			$number = $this->ObtainCalculator()->fromArbitraryBase($baseN, $alphabet, \strlen($alphabet));
		}

		$this->assertSame($base10, $number);
	}

	/**
	 * @return Generator<int, array{0:string, 1:string, 2:string}, mixed, void>
	 */
	public function providerFromArbitraryBase() : Generator
	{
		foreach ($this->providerArbitraryBase() as [$base10, $alphabet, $baseN]) {
			yield [$base10, $alphabet, $baseN];

			// test with a number of leading "zeros"
			yield [$base10, $alphabet, $alphabet[0] . $baseN];
			yield [$base10, $alphabet, $alphabet[0] . $alphabet[0] . $baseN];
		}
	}

	/**
	 * @dataProvider providerArbitraryBase
	 *
	 * @param string $base10
	 */
	public function testToArbitraryBase($base10, string $alphabet, string $baseN) : void
	{
		$actual = $this->ObtainCalculator()->toArbitraryBase($base10, $alphabet, \strlen($alphabet));

		$this->assertSame($baseN, $actual);
	}

	/**
	 * @return list<array{0:string, 1:string, 2:string}>
	 */
	public function providerArbitraryBase() : array
	{
		$base7 = '0123456';
		$base8 = '01234567';
		$base9 = '012345678';
		$base10 = '0123456789';
		$base11 = '0123456789A';
		$base12 = '0123456789AB';
		$base13 = '0123456789ABC';
		$base14 = '0123456789ABCD';
		$base15 = '0123456789ABCDE';
		$base16 = '0123456789ABCDEF';
		$base17 = '0123456789ABCDEFG';
		$base64 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/';
		$base72 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~_!$()+,;@';
		$base85 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-:+=^!/*?&<>()[]{}@%$#';
		$base95 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~_!$()+,;@.:=^*?&<>[]{}%#|`/\ "\'-';

		$base62LowerUpper = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base62UpperLower = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		return [
			['0', 'XY', 'X'],
			['1', 'XY', 'Y'],
			['2', 'XY', 'YX'],
			['3', 'XY', 'YY'],
			['4', 'XY', 'YXX'],

			['1234567890', '9876543210', '8765432109'],
			['9876543210', '1234567890', '0987654321'],

			['98719827932647929837981791821991234', '01234567', '460150331736165026742535432255203706502'],
			['98719827932647929837981791821991234', 'ABCDEFGH', 'EGABFADDBHDGBGFACGHECFDFEDCCFFCADHAGFAC'],

			['994495526373444232246567036253784322009', $base7, '12202520340634022241654466246440466210615152466'],
			['994495526373444232246567036253784322009', $base8, '13541315742261267512021577112421152053227731'],
			['994495526373444232246567036253784322009', $base9, '66488066070032874134652704428716607733277'],
			['994495526373444232246567036253784322009', $base10, '994495526373444232246567036253784322009'],
			['994495526373444232246567036253784322009', $base11, '2A1978399765A213135A156506809522356825'],
			['994495526373444232246567036253784322009', $base12, '14A05B751367AA17A09769472516764A47821'],
			['994495526373444232246567036253784322009', $base13, '103A050CB893910A25357BB9C395A51C0814'],
			['994495526373444232246567036253784322009', $base14, '10D925D22C52737225B8D5644D989CD666D'],
			['994495526373444232246567036253784322009', $base15, '180B5C6CC477E8D58EAC276D06C5127124'],
			['994495526373444232246567036253784322009', $base16, '2EC2CDF12C56F4A08DFC9511350AD2FD9'],
			['994495526373444232246567036253784322009', $base17, '7266E944CF4A3786D0G7661356FG769G'],

			['994495526373444232246567036253784322009', $base62LowerUpper, 'mLMLxPbmO0SM6PXtWChlWx'],
			['994495526373444232246567036253784322009', $base62UpperLower, 'MlmlXpBMo0sm6pxTwcHLwX'],

			['8149613250471589625', $base64, '74PFXAZBFRv'],
			['454064679874654562007441356949657', $base64, '1PZ6Xm9ayTgCZU5xGYP'],
			['45422310646719874654562007441356949657', $base64, 'YB0JHWGUe4+J+zbWTxGYP'],
			['1121921454223110646719874654562007441356949657', $base64, 'oJmRKAU1GNNBHSz/S2Q0TxGYP'],
			['10121192145422311064671918746545620075441356949657', $base64, '1kpPyynJk/pgMxgIopD9BB+eAaYP'],

			['91906824217328753670', $base72, 'OdYuzDmu@os'],
			['535903357336880946855837144765', $base72, '11;QwdMB!D)84;w@,'],
			['3628645428648421468982810963905568210330', $base72, '3g_D_hpFwvT+jM2UiUF$eQ'],
			['67461606287909524242401421486420908853942741199316', $base72, 'YdSH9KcqwE)dahLuF(uO,s2Y8Di'],
			['673058295257771060991298040835276179059500055157907555831688', $base72, '2Y9hTIpkORoK;$uQNC!8u$1~9RBE1QRVG'],

			['79248970614563033069', $base85, '42dhgJI>!D{'],
			['70259972284912331680149126100', $base85, '!vcNSNE+R.X.t$k'],
			['1345211446421580809283013645361855592276', $base85, '3E0H]t@k=%[$4EHpk/WV6'],
			['92817563463558871408829910215554937029176299613741', $base85, 'R%GwIo]>peBh?fLxaPWsYp%I16'],
			['105332456216236666737534759570691835270616864952881377663761', $base85, 'd!qFsWcY1Q6IuAU{50jN6?nK=lFms11'],

			['82164606170768213165', $base95, '1ZY-^xBX,-A'],
			['524820792661006993039498194693', $base95, '1Cwv({YtIbrPpE]r'],
			['2500692630577003661291596854860146627030', $base95, '(Pszub<V3^Y]cs\YnU}o'],
			['76088698829341245347114640636745832062447993955533', $base95, '2;t?i8zv}hWZ> )loCj(d7*yO3'],
			['949872477171550708823123033931693463913459064733934993892215', $base95, '4ed~yPcS~L3d)w}!A!%R5_4Dx9u;B?0'],

			['994495526373444232246567036253784322009', Calculator::ALPHABET, '18akqxs3wbt1320enhlitkne8p'],
		];
	}

	/**
	 * @dataProvider providerArbitraryBaseWithInvalidAlphabet
	 */
	public function testFromArbitraryBaseWithInvalidAlphabet(string $alphabet) : void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('The alphabet must contain at least 2 chars.');

		$this->ObtainCalculator()->fromArbitraryBase('0', $alphabet, 2);
	}

	/**
	 * @dataProvider providerFromArbitraryBaseWithInvalidNumber
	 */
	public function testFromArbitraryBaseWithInvalidNumber(string $number, string $alphabet, string $expectedMessage) : void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage($expectedMessage);

		$this->ObtainCalculator()->fromArbitraryBase($number, $alphabet, 2);
	}

	/**
	 * @return list<array{0:string, 1:string, 2:string}>
	 */
	public function providerFromArbitraryBaseWithInvalidNumber() : array
	{
		return [
			['', '01', 'The number cannot be empty.'],
			['X', '01', 'Char "X" is not a valid character in the given alphabet.'],
			['1', 'XY', 'Char "1" is not a valid character in the given alphabet.'],
			[' ', 'XY', 'Char " " is not a valid character in the given alphabet.'],

			["\x00", '01', 'Char 00 is not a valid character in the given alphabet.'],
			["\x1F", '01', 'Char 1F is not a valid character in the given alphabet.'],
			["\x7F", '01', 'Char 7F is not a valid character in the given alphabet.'],
			["\x80", '01', 'Char 80 is not a valid character in the given alphabet.'],
			["\xFF", '01', 'Char FF is not a valid character in the given alphabet.'],
		];
	}

	/**
	 * @dataProvider providerArbitraryBaseWithInvalidAlphabet
	 */
	public function testToArbitraryBaseWithInvalidAlphabet(string $alphabet) : void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('The alphabet must contain at least 2 chars.');

		$this->ObtainCalculator()->toArbitraryBase('123', $alphabet, 2);
	}

	/**
	 * @return list<array{0:string}>
	 */
	public function providerArbitraryBaseWithInvalidAlphabet() : array
	{
		return [
			[''],
			['0'],
		];
	}

	public function testToArbitraryBaseOnNegativeNumber() : void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('toArbitraryBase() does not support negative numbers.');

		$this->ObtainCalculator()->toArbitraryBase('-123', '01', 2);
	}

	public function testNeg() : void
	{
		static::assertSame('-1', $this->ObtainCalculator()->neg('1'));
		static::assertSame('0', $this->ObtainCalculator()->neg('0'));
		static::assertSame('1', $this->ObtainCalculator()->neg('-1'));
	}

	public function testDivQR() : void
	{
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '-1'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '-2'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '-3'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '0'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '1'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '2'));
		static::assertSame(['0', '0'], $this->ObtainCalculator()->divQR('0', '3'));
		static::assertSame(['1', '0'], $this->ObtainCalculator()->divQR('1', '1'));
		static::assertSame(['2', '0'], $this->ObtainCalculator()->divQR('2', '1'));
		static::assertSame(['-1', '0'], $this->ObtainCalculator()->divQR('1', '-1'));
		static::assertSame(['-2', '.1'], $this->ObtainCalculator()->divQR('4.3', '-2.1'));
		static::assertSame(['2', '-.1'], $this->ObtainCalculator()->divQR('-4.3', '-2.1'));
		static::assertSame(['-2', '-.1'], $this->ObtainCalculator()->divQR('-4.3', '2.1'));
	}

	abstract protected function ObtainCalculator() : Calculator;
}
