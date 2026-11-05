# Laravel dump server

Maintained fork for PHP 8.2–8.5, Laravel 12/13 and Symfony VarDumper 7.4/8. Based on [Beyond Code](https://github.com/beyondcode/laravel-dump-server) and [Laravel Shift's Laravel 13 compatibility branch](https://github.com/laravel-shift/laravel-dump-server/tree/l13-compatibility).

The server collects `dump()` output over TCP. While the receiver runs, dumps appear in its terminal instead of the application's HTTP response. If the receiver is unavailable, dumps fall back to the application's normal CLI or HTML output. That fallback can change an API response, so keep the receiver running when debugging JSON endpoints.

## Requirements

| Dependency | Supported constraint |
| --- | --- |
| PHP | `^8.2` |
| Illuminate Console, HTTP and Support | `^12.0 \|\| ^13.0` |
| Symfony VarDumper | `^7.4 \|\| ^8.0` |

Composer selects compatible dependencies for your PHP version. Laravel 12 uses VarDumper 7.4. Laravel 13 requires PHP 8.3 or later and can use VarDumper 7.4 or 8. Symfony VarDumper 8 requires PHP 8.4 or later; PHP 8.2 and 8.3 use VarDumper 7.4.

## Install

Install the maintained package from Packagist as a development dependency:

```bash
composer require --dev gracjankubicki/laravel-dump-server:^3.1
```

No custom Composer repository is needed. The namespace remains `BeyondCode\DumpServer`, and Laravel discovers the service provider automatically. The package conflicts with `beyondcode/laravel-dump-server` and `spatie/laravel-ray` to avoid duplicate dump handlers.

## Migrate from Beyond Code or the VCS fork

Remove the old requirement without updating dependencies yet, then require the new package:

```bash
composer remove --dev beyondcode/laravel-dump-server --no-update
composer require --dev gracjankubicki/laravel-dump-server:^3.1 --with-all-dependencies
```

You can remove the old dump-server VCS repository entry from `composer.json`. Keep unrelated repository entries. The command, namespace, configuration, CLI/HTML formats and local fallback are unchanged.

If your application's `extra.laravel.dont-discover` lists `beyondcode/laravel-dump-server`, replace that entry with `gracjankubicki/laravel-dump-server` to keep automatic provider registration disabled. Otherwise the renamed package will be discovered. Explicit provider class registrations remain valid.

Commit the resulting application `composer.lock`. The library's development tools are not installed as dependencies in your application.

## Run the receiver

```bash
php artisan dump-server
```

For HTML output:

```bash
php artisan dump-server --format=html > dumps.html
```

The default address is `tcp://127.0.0.1:9912`. Override it for both the application and the receiver with `DUMP_SERVER_HOST`. To publish the configuration:

```bash
php artisan vendor:publish --provider='BeyondCode\DumpServer\DumpServerServiceProvider' --tag=config
```

The resulting file is `config/debug-server.php`. Restart long-running application processes after changing configuration.

Use this package only in a trusted development environment. The TCP receiver has no authentication or encryption, and accepts serialized debug data. Keep it on loopback or an isolated development network. Dumps can contain application secrets. The provider registers its handler when loaded; it does not enforce `APP_DEBUG` or an environment check.

## Develop and verify

The Docker wrapper runs PHP and Composer without a host PHP installation:

```bash
make build
make install
make check
```

`make check` checks formatting with Pint and runs the selected Rector rules in dry-run mode, PHPUnit, Larastan at its maximum level, and Composer audit. Integration tests start real loopback TCP and HTTP servers, verify CLI and HTML output, preserve a JSON response, and exercise fallback and command failures. Processes and sockets are closed by the tests. Compatibility tests also protect existing untyped overrides in subclasses.

Run `make format` to apply the Laravel Pint preset. `make rector` checks switch-to-match conversions in `src`. Constructor promotion is deliberately excluded: nullable properties must keep their declaration defaults for subclasses that do not call the parent constructor. Public return types remain unchanged so existing subclasses can keep their overrides.

CI tests seven combinations of PHP 8.2–8.5, Laravel 12/13 and VarDumper 7.4, 8.0 and current 8.x. Each combination runs the real TCP/HTTP tests, maximum-level analysis and audit. Formatting and Rector run on PHP 8.5.

To reproduce an isolated compatibility case without modifying your local vendor directory:

```bash
make matrix-case PHP_VERSION=8.2 TESTBENCH='^10.0' VAR_DUMPER='^7.4'
```

`make build` defaults to PHP 8.5. You can override `PHP_VERSION` for another supported version. The library does not commit `composer.lock`; consuming applications must commit their own lock file. Dependabot checks Composer constraints and GitHub Actions weekly. A passing audit means no advisories were reported for the resolved dependencies at that time.

## Contribute and report vulnerabilities

See [CONTRIBUTING.md](CONTRIBUTING.md). For vulnerabilities in this fork, contact [gracjankubicki](https://github.com/gracjankubicki) privately through a contact method listed on the profile. Do not post sensitive reproduction data in a public issue or pull request.

## Credits and license

Original implementation by [Marcel Pociot](https://github.com/mpociot) and [contributors](https://github.com/beyondcode/laravel-dump-server/graphs/contributors). Laravel 13 compatibility work by Laravel Shift. This fork is maintained by [Gracjan Kubicki](https://github.com/gracjankubicki).

Released under the [MIT license](LICENSE.md). The original copyright and license are preserved.
