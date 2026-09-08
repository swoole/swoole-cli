# PHP Runtime Layer

`swoole-cli` only produces the native PHP runtime consumed by the PHPX SDK
pipeline. Complete developer SDKs are assembled and published by
[`swoole/phpx`](https://github.com/swoole/phpx/releases).

The runtime output contains:

- `lib/libphp.a`: PHP, Zend, TSRM, enabled extensions and their static
  dependencies;
- `include/php/`: matching PHP, Zend, TSRM, extension and Embed SAPI headers;
- `include/`: headers belonging to static third-party dependencies;
- `lib/musl/*.o` on Linux: startup objects required for a full-static link;
- ABI and source-version manifests.

It deliberately does not contain `libphpx.a`, PHPX headers, or a complete PHPX
SDK. Those are owned by the PHPX repository.

## Linux

Build `swoole-cli` and the self-contained PHP archive in the normal Alpine
builder, then package the target runtime layer:

```sh
php prepare.php --with-static-pie --with-libavif
./make.sh all-library
./make.sh config
./make.sh build
./make.sh libphp
bash sapi/scripts/package-php-runtime-layer.sh linux-x64
```

Use `linux-arm64` on an arm64 builder. The resulting archive and SHA-256 file
are written below `runtime-layer/`. Release-tag workflows attach them to the
corresponding `swoole-cli` release for the PHPX pipeline to consume.

## Ownership boundary

The generated `make.sh` has no `phpx` or `sdk` targets. Building `libphpx.a`,
adding PHPX headers, final link validation, SDK packaging, and user-facing SDK
release assets all happen in `swoole/phpx`.
