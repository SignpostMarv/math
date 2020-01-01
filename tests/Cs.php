<?php

declare(strict_types=1);

namespace SignpostMarv\Brick\Math\Tests;

use SignpostMarv\CS\ConfigUsedWithStaticAnalysis;

class Cs extends ConfigUsedWithStaticAnalysis
{
	protected static function RuntimeResolveRules() : array
	{
		$rules = parent::RuntimeResolveRules();

		$rules['mb_str_functions'] = false;

		return $rules;
	}
}
