# Laravel dump server

Maintained fork for PHP 8.5, Laravel 13 and Symfony VarDumper 8.1. Based on [Beyond Code](https://github.com/beyondcode/laravel-dump-server) and [Laravel Shift's Laravel 13 compatibility branch](https://github.com/laravel-shift/laravel-dump-server/tree/l13-compatibility).

The server collects `dump()` output over TCP. While the receiver runs, dumps appear in its terminal instead of the application's HTTP response. If the receiver is unavailable, dumps fall back to the application's normal CLI or HTML output. That fallback can change an API response, so keep the receiver running when debugging JSON endpoints.

## Requirements

| Dependency | Supported constraint |
| --- | --- |
| PHP | `^8.5` |
| Illuminate Console, HTTP and Support | `^13.30` |
| Symfony VarDumper | `^8.1.10` |

Version 3 drops older PHP, Laravel and Symfony versions. The package name, `BeyondCode\DumpServer` namespace, automatic provider discovery, `dump-server` command and `debug-server.host` configuration remain unchanged. This fork retains the existing conflict with `spatie/laravel-ray`.

## Install this fork

This release is distributed through GitHub VCS, not Packagist. Add the repository to your application's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/gracjankubicki/laravel-dump-server"
    }
  ]
}
```

Then install the tagged fork as a development dependency:

```bash
composer require --dev 'beyondcode/laravel-dump-server:^3.0' --with-all-dependencies
```

If the application already has a repositories list, append this entry without replacing the other entries. Keep the package name `beyondcode/laravel-dump-server`; it identifies this fork through the VCS repository above.

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

`make check` runs PHPUnit, Larastan at level 8, and Composer audit. Integration tests start real loopback TCP and HTTP servers, verify CLI and HTML output, preserve a JSON response, and exercise fallback and command failures. Processes and sockets are closed by the tests.

CI checks current dependency resolution on PHP 8.5. The library does not commit `composer.lock`; consuming applications must commit their own lock file. Dependabot checks Composer constraints and GitHub Actions weekly. A passing audit means no advisories were reported for the resolved dependencies at that time.

## Contribute and report vulnerabilities

See [CONTRIBUTING.md](CONTRIBUTING.md). For vulnerabilities in this fork, contact [gracjankubicki](https://github.com/gracjankubicki) privately through a contact method listed on the profile. Do not post sensitive reproduction data in a public issue or pull request.

## Credits and license

Original implementation by [Marcel Pociot](https://github.com/mpociot) and [contributors](https://github.com/beyondcode/laravel-dump-server/graphs/contributors). Laravel 13 compatibility work by Laravel Shift. This fork is maintained by [Gracjan Kubicki](https://github.com/gracjankubicki).

Released under the [MIT license](LICENSE.md). The original copyright and license are preserved.
