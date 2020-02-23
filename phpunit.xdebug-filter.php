<?php
/**
* PHPUnit XDebug Filter.
*/
declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests;

use function function_exists;
use function realpath;
use const XDEBUG_FILTER_CODE_COVERAGE;
use const XDEBUG_PATH_WHITELIST;
use function xdebug_set_filter;

if ( ! function_exists('xdebug_set_filter')) {
	return;
}

xdebug_set_filter(
	XDEBUG_FILTER_CODE_COVERAGE,
	XDEBUG_PATH_WHITELIST,
	[
		realpath(__DIR__ . '/src/'),
	]
);
