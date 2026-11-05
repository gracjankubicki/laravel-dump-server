# Changelog

## 3.1.0

- Publish as `gracjankubicki/laravel-dump-server` for installation from Packagist without VCS configuration.
- Support PHP 8.2–8.5, Laravel 12/13 and Symfony VarDumper 7.4/8, verified by a seven-case test matrix.
- Conflict with the old package to prevent two packages providing the same namespace.
- Keep the `BeyondCode\DumpServer` namespace, command, configuration, transport and fallback behavior.
- Document migration, including preserving `dont-discover` settings when changing package names.

### Code standards included in 3.1.0

- Add native private property types and use match for output format selection.
- Validate host and controller types with explicit errors for invalid input.
- Preserve public method overrides and protected property compatibility.
- Add targeted Rector and Pint checks, maximum-level Larastan, and regression tests for configuration, controllers and subclasses.

## 3.0.0

- Require PHP 8.5, Laravel 13.30 or later in the Laravel 13 series, and Symfony VarDumper 8.1.10 or later in the Symfony 8 series.
- Preserve the package name, provider discovery, CLI and HTML formats, configuration and local fallback behavior.
- Resolve configuration and HTTP request services through container contracts for static analysis.
- Validate the command format type before selecting a descriptor.
- Replace obsolete development dependencies with PHPUnit 13, Testbench 11 and Larastan 3.
- Add real TCP and HTTP integration tests, fallback and command failure tests, Docker tooling, CI and dependency update checks.
- Distribute the maintained fork through GitHub VCS under the original MIT license.

All notable changes to `laravel-dump-server` will be documented in this file

## Unreleased

- laravel 9 support

## 1.0.0 - 2018-07-09

- initial release
