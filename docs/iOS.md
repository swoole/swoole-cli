# iPhoneOS arm64 PHP Runtime Layer

The iPhoneOS build is a cross target hosted on macOS with full Xcode. GitHub's
macOS runner images include Xcode and an iPhoneOS SDK, so the build does not
need a physical device or code signing.

```sh
xcodebuild -version
xcrun --sdk iphoneos --show-sdk-path
xcrun --sdk iphoneos --show-sdk-version
```

Build the runtime for a physical arm64 iPhone:

```sh
php prepare.php @iphoneos-arm64 --with-parallel-jobs=8
./make.sh all-library
./make.sh config
./make.sh libphp
bash sapi/scripts/package-php-runtime-layer.sh iphoneos-arm64
```

`make.sh libphp` combines the PHP archive with the target GMP, GMP C++ and MPFR
objects into one `libphp.a`. Apple libc and libc++ remain platform libraries
supplied by the final Xcode link. The runtime archive contains `libphp.a` and
the matching headers, but never contains `libphpx.a` or a complete SDK.

The profile targets `arm64-apple-ios15.0`, uses ZTS, and intentionally enables
only bcmath, ctype, filter and gmp. Server, process, JIT and dynamic extension
facilities are excluded from the mobile runtime.

The `build-php-runtime-iphoneos-arm64` workflow validates the installed SDK,
cross-compiles the runtime, checks the Mach-O archive architecture, and uploads
the immutable Runtime Layer. The PHPX workflow downloads that layer and owns
the final SDK assembly and release.
