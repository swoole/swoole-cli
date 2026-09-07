# iPhoneOS arm64 SDK

The iPhoneOS build is a cross target hosted on macOS. It does not change the
host operating-system model and it never uses Docker. Full Xcode is required;
Command Line Tools do not contain the iPhoneOS SDK.

```sh
sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
xcrun --sdk iphoneos --show-sdk-path
```

Generate the dedicated build profile and build its dependencies:

```sh
php prepare.php @iphoneos-arm64 --with-parallel-jobs=8
./make.sh all-library
./make.sh config
./make.sh libphp
./make.sh phpx
./make.sh sdk
```

The build uses `arm64-apple-ios15.0`, ZTS, static PHP/PHPX/dependency archives,
and a target-only dependency prefix under `var/iphoneos-arm64`. The integrated
SDK is staged directly in:

```text
thirdparty/phpx/ios/iphoneos-arm64/
```

The final `sdk` command also creates a distributable archive under `sdk/`.
TypePHP resolves the installed directory through `PHPX_HOME`, in the same way
that it resolves `full-static/sdk` and `wasm/wasm32-wasip2`; `PHP_HOME` is not
used for this cross target.

## Extension profile

The first mobile profile intentionally enables only:

- bcmath
- ctype
- filter
- gmp, together with the MPFR library required by PHPX

It disables server- and process-oriented facilities such as Opcache/JIT,
pcntl, POSIX, sockets, Swoole, Redis, MongoDB, MySQL clients, readline, and
other PECL services. curl/OpenSSL and SQLite can be introduced later as
separately tested mobile capability layers; they are not prerequisites for the
TypePHP UIKit example.

PHP fibers remain available, but `fiber-asm` is disabled. This keeps PHP's
assembly context switcher from conflicting with Swoole's coroutine context
switching implementation.

Apple libc, libc++, UIKit, and Foundation remain platform libraries. They are
linked by the final application build and must not be copied or merged into
`libphp.a`.
