## SignpostMarv\Brick Math Base Convert

A fork of [brick/math](https://github.com/brick/math), extracting & refactoring the base conversion portion.

[![Build Status](https://api.travis-ci.org/signpostmarv/brick-math-base-convert.svg?branch=master)](http://travis-ci.org/signpostmarv/brick-math-base-convert)
[![Coverage Status](https://coveralls.io/repos/github/SignpostMarv/brick-math-base-convert/badge.svg?branch=master)](https://coveralls.io/github/SignpostMarv/brick-math-base-convert?branch=master)
[![Total Downloads](https://poser.pugx.org/signpostmarv/brick-math-base-convert/downloads)](https://packagist.org/packages/signpostmarv/brick-math-base-convert)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

### Installation

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require signpostmarv/brick-math-base-convert
```

### Requirements

This library requires PHP 7.4 or later.

For older version, please use the [original package](https://github.com/brick/math).

Although the library can work seamlessly on any PHP installation, implementations for [ext-bcmath](https://github.com/signpostmarv/brick-math-base-convert-bcmath) and [ext-gmp](https://github.com/signpostmarv/brick-math-base-convert-gmp) are available.

### Project status & release process

While this library is still under development, it is well tested and should be stable enough to use in production
environments.

The current releases are numbered `0.x.y`. When a non-breaking change is introduced (adding new methods, optimizing
existing code, etc.), `y` is incremented.

**When a breaking change is introduced, a new `0.x` version cycle is always started.**

It is therefore safe to lock your project to a given release cycle, such as `0.8.*`.

If you need to upgrade to a newer release cycle, check the [release history](https://github.com/signpostmarv/brick-math-base-convert/releases)
for a list of changes introduced by each further `0.x.0` version.
