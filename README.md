# swoole-cli

> Language: **English** | [中文](README.zh-CN.md)

## What is swoole-cli?

`swoole-cli` is a **standalone, statically-linked PHP binary distribution**.

It packages the PHP engine (CLI/FPM), the Swoole extension, and many commonly used
extensions into a single executable. Everything is compiled statically — it has no
dependency on system shared libraries (`*.so`), so you can copy it between machines
and run it immediately.

Unlike the traditional "PHP + swoole extension" model, Swoole runs here as an
independent program (similar to Node.js), not as a PHP add-on. The project also trims
`php-src` aggressively so the whole binary can be built in a few minutes.

### Highlights

- Single static binary, zero system runtime dependencies, high portability
- Built-in `swoole`, `php-cli`, `php-fpm` and a rich set of common extensions
- Download & run — no installation required
- Source build pipeline generates `make.sh` for one-command builds on each platform

## Download & quick start

Release binaries are available at:

- https://www.swoole.com/download
- https://github.com/swoole/swoole-cli/releases

Install the latest release:

```shell
# Linux / macOS
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash

# Windows (PowerShell)
irm https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.ps1?raw=true | iex
```

Optional flags (see `setup-swoole-cli-runtime.sh -h`):

```shell
# Use the China mirror (https://www.swoole.com/download)
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --mirror china

# Pin a specific release version
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --version v6.2.2.1
```

## Supported platforms

| Platform | Target | Artifact |
|---|---|---|
| Linux x86_64 | `swoole-cli` | Full static binary (`.tar.xz`) |
| Linux aarch64 | `swoole-cli` | Full static binary (`.tar.xz`) |
| macOS arm64 | `swoole-cli` | Full static binary (`.tar.xz`) |
| iPhoneOS arm64 | PHP Runtime Layer | `libphp.a` runtime layer (`runtime-layer/*.tar.xz`) |
| Android arm64-v8a | PHP Runtime Layer | `libphp.a` runtime layer (`runtime-layer/*.tar.xz`) |
| Windows | — | Native Windows support is under development. The legacy Cygwin/MSYS2 build scripts have been discontinued (kept under `.github/backup/`). |

## Build from source

### Prerequisites

- `git`, PHP 8.x (`php-cli` with `curl`/`json`/`posix`) or run `bash setup-php-runtime.sh`
  to obtain a managed build PHP runtime
- `composer`
- `docker` for the default Linux container build (not required for `--without-docker`)

### Common pipeline

```shell
git clone https://github.com/swoole/swoole-cli.git && cd swoole-cli

# 1. prepare the host PHP runtime (optional; used to run composer/prepare.php)
bash setup-php-runtime.sh

# 2. install composer dependencies and generate make.sh
composer install
php prepare.php               # add/remove extensions: php prepare.php +inotify -mysqli

# 3. build
./make.sh all-library         # build the third-party C/C++ dependency libraries
./make.sh config              # configure the PHP build
./make.sh build               # compile swoole-cli -> bin/swoole-cli
./make.sh archive             # package into swoole-cli-{version}-{os}-{arch}.tar.xz
```

Use `+{ext}` / `-{ext}` to enable / disable extensions, `@{os}` to pick a build
target, and `--` options to tweak the build. See [docs/options.md](docs/options.md).

### Linux (x86_64 / aarch64)

The default pipeline runs inside an Alpine container for a fully static `glibc`-free
build:

```shell
./make.sh docker-bash        # enter the builder container (repo mapped to /work)
# inside the container:
composer install
php prepare.php --with-static-pie --with-libavif
bash make.sh all-library
bash make.sh config
bash make.sh build
bash make.sh archive
```

To build directly on a Linux host with the system toolchain, pass `--without-docker`:

```shell
php prepare.php --without-docker --with-libavif
./make.sh all-library && ./make.sh config && ./make.sh build
```

Details: [docs/linux.md](docs/linux.md)

### macOS (arm64)

```shell
bash sapi/quickstart/macos/macos-init.sh   # install brew toolchain deps
php prepare.php --without-docker=1 --with-libavif
./make.sh all-library
./make.sh config
./make.sh build
./make.sh archive
```

Details: [docs/macOS.md](docs/macOS.md)

### iPhoneOS / Android (PHP Runtime Layer)

Cross-compile a static `libphp.a` PHP runtime layer for mobile targets; the final
SDK is assembled by the consuming project (e.g. swoole/phpx):

```shell
# iPhoneOS arm64
php prepare.php @iphoneos-arm64 --with-parallel-jobs=3
bash make.sh all-library
bash make.sh config
bash make.sh libphp
GLOBAL_PREFIX="$PWD/var/iphoneos-arm64/deps" \
  bash sapi/scripts/package-php-runtime-layer.sh iphoneos-arm64

# Android arm64-v8a (requires Android NDK)
php prepare.php @android-arm64-v8a --with-parallel-jobs=4
bash make.sh all-library
bash make.sh config
bash make.sh libphp
GLOBAL_PREFIX="$PWD/var/android-arm64-v8a/deps" \
  bash sapi/scripts/package-php-runtime-layer.sh android-arm64-v8a
```

Details: [docs/iOS.md](docs/iOS.md) · [docs/sdk.md](docs/sdk.md) · [docs/libphp.md](docs/libphp.md)

### Related docs

- [Build options](docs/options.md) · [Common questions](docs/FAQ.md)
- [Extended extensions](docs/extensions.md) · [Dependency graph](docs/dependency-graph.md)
- [Quickstart build environment](sapi/quickstart/README.md)
- [Host a dependency-source mirror](sapi/download-box/README.md)

## License

- `swoole-cli` bundles many other open-source projects. Please review the license
  notices in the auto-generated `bin/LICENSE` and comply with each upstream license.
- The source code and documentation of `swoole-cli` itself are dual-licensed under
  **Apache 2.0 LICENSE** and **SWOOLE-CLI LICENSE**; both licenses must be observed.

### SWOOLE-CLI LICENSE

- Any new project that uses, modifies, or distributes `swoole-cli` code must retain
  all contents of the SWOOLE-CLI LICENSE.
- When re-publishing `swoole-cli` code as a new project or product, the project or
  product name must not contain the word `swoole`.
