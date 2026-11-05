# Changelog

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
