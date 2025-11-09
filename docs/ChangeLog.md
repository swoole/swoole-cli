## v6.1.1.0

### 新增

1. gcc 动态版的 swoole-cli
2. 新增 `random` 扩展
3. curl 启用 `libind2` 库，支持中文域名解析
4. curl 启用 `libpsl`  库，支持检查域名合法性

### 变更

1. PHP 版本由 `8.1` (8.1.29) 升级为 `8.4` (8.4.14)
2. github macos amd64 构建环境由 `macos-13` 升级为 `macos-15-intel`
3. github macos arm64 构建环境由 `macos-14` 升级为 `macos-15`
4. 扩展 `swoole`  版本由 `v5.1.x`
   升级为 `v6.1.1`，[swoole v6.1.1 info](https://github.com/swoole/swoole-src/releases/tag/v6.1.0)
5. 扩展 `redis`   版本由 `5.3.7`  升级为 `6.2.0`
6. 扩展 `imagick` 版本由 `3.6.0`  升级为 `3.8.0`
7. `openssl` 库版本由 `v3.1.4`  升级为 `v3.6.0` ( openssl 3.5 支持了 quic 协议堆栈)
8. `curl`    库版本由 `v8.4.0`  升级为 `v8.16.0`
9. `nghttp2` 库版本由 `v1.57.0` 升级为 `v1.68.0`
10. `nghttp3` 库版本由 `v1.0.0`  升级为 `v1.12.0`
11. `ngtcp2`  库版本由 `8.4.0`   升级为 `8.16.0`
12. `curl` 库支持的 `http3` 协议，实现由(`quictls + ngtcp2 + nghttp3`) 变更为 (`openssl + nghttp3`)

### 优化

1. 修复 macos 构建环境下 `libssh2` 功能出现废弃的警告

### Bug 修复

1. 修复 macos 构建环境下 `gmp` 库链接错误 (启用`-fPIC`)

### 废弃

1. `ngtcp2` 库冻结使用

## v5.1.8.0

1. [change info](https://github.com/swoole/swoole-cli/compare/v5.1.7.0...v5.1.8.0)
1. swoole version v5.1.7 upgrade to
   v5.1.8 , [swoole v5.1.8 info](https://github.com/swoole/swoole-src/releases/tag/v5.1.8)
