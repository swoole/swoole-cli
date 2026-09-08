# swoole-cli

> 语言：**中文** | [English](README.md)

## swoole-cli 是什么？

`swoole-cli` 是一个 **独立、静态编译的 PHP 二进制发行版**。

它将 PHP 内核（CLI/FPM）、Swoole 扩展以及大量常用扩展打包进单一可执行文件。
全部采用静态编译，不依赖操作系统的任何动态链接库（`*.so`），因此可以跨机器
直接拷贝、下载即可运行。

与传统的 "PHP + swoole 扩展" 模式不同，Swoole 在这里是作为独立程序提供给用户
（类似 Node.js），而不是作为 PHP 的一个扩展。项目还会对 `php-src` 进行大量裁剪，
使整个程序能在几分钟内编译完成。

### 特性

- 单一静态二进制，零系统运行依赖，可移植性强
- 内置 `swoole`、`php-cli`、`php-fpm` 以及丰富的常用扩展
- 下载即用，无需安装
- 提供源码构建流程，自动生成 `make.sh`，各平台一键构建

## 下载与快速使用

发行版下载地址：

- https://www.swoole.com/download
- https://github.com/swoole/swoole-cli/releases

安装最新版本：

```shell
# Linux / macOS
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash

# Windows (PowerShell)
irm https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.ps1?raw=true | iex
```

更多参数（查看 `setup-swoole-cli-runtime.sh -h`）：

```shell
# 使用国内镜像（https://www.swoole.com/download）
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --mirror china

# 指定发布版本
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --version v6.2.2.1
```

## 支持的系统与平台

| 平台 | 目标 | 产物 |
|---|---|---|
| Linux x86_64 | `swoole-cli` | 完整静态二进制（`.tar.xz`） |
| Linux aarch64 | `swoole-cli` | 完整静态二进制（`.tar.xz`） |
| macOS arm64 | `swoole-cli` | 完整静态二进制（`.tar.xz`） |
| iPhoneOS arm64 | PHP Runtime Layer | `libphp.a` 运行层（`runtime-layer/*.tar.xz`） |
| Android arm64-v8a | PHP Runtime Layer | `libphp.a` 运行层（`runtime-layer/*.tar.xz`） |
| Windows | — | 原生 Windows 支持开发中；旧的 Cygwin/MSYS2 构建脚本已停用（归档于 `.github/backup/`） |

## 源码构建

### 前置条件

- `git`、PHP 8.x（`php-cli`，含 `curl`/`json`/`posix`），或执行
  `bash setup-php-runtime.sh` 获取托管构建用 PHP 运行时
- `composer`
- 默认 Linux 容器构建需要 `docker`（使用 `--without-docker` 时不需要）

### 通用构建流程

```shell
git clone https://github.com/swoole/swoole-cli.git && cd swoole-cli

# 1. 准备宿主 PHP 运行时（可选，用于执行 composer/prepare.php）
bash setup-php-runtime.sh

# 2. 安装 composer 依赖并生成 make.sh
composer install
php prepare.php               # 增减扩展：php prepare.php +inotify -mysqli

# 3. 构建
./make.sh all-library         # 编译第三方 C/C++ 依赖库
./make.sh config              # 配置 PHP 编译
./make.sh build               # 编译 swoole-cli -> bin/swoole-cli
./make.sh archive             # 打包成 swoole-cli-{version}-{os}-{arch}.tar.xz
```

使用 `+{ext}` / `-{ext}` 增减扩展，`@{os}` 选择构建目标，`--` 调整构建选项，
详见 [docs/options.md](docs/options.md)。

### Linux（x86_64 / aarch64）

默认在 Alpine 容器内构建，产出完全静态、不依赖 glibc 的二进制：

```shell
./make.sh docker-bash        # 进入构建容器（仓库映射到 /work）
# 容器内执行：
composer install
php prepare.php --with-static-pie --with-libavif
bash make.sh all-library
bash make.sh config
bash make.sh build
bash make.sh archive
```

如需直接使用 Linux 宿主机系统工具链构建，加 `--without-docker`：

```shell
php prepare.php --without-docker --with-libavif
./make.sh all-library && ./make.sh config && ./make.sh build
```

详见 [docs/linux.md](docs/linux.md)

### macOS（arm64）

```shell
bash sapi/quickstart/macos/macos-init.sh   # 安装 brew 工具链依赖
php prepare.php --without-docker=1 --with-libavif
./make.sh all-library
./make.sh config
./make.sh build
./make.sh archive
```

详见 [docs/macOS.md](docs/macOS.md)

### iPhoneOS / Android（PHP Runtime Layer）

为移动端目标交叉编译静态 `libphp.a` PHP 运行层；最终 PHPX SDK 由消费方项目
（如 swoole/phpx）组装发布：

```shell
# iPhoneOS arm64
php prepare.php @iphoneos-arm64 --with-parallel-jobs=3
bash make.sh all-library
bash make.sh config
bash make.sh libphp
GLOBAL_PREFIX="$PWD/var/iphoneos-arm64/deps" \
  bash sapi/scripts/package-php-runtime-layer.sh iphoneos-arm64

# Android arm64-v8a（需要 Android NDK）
php prepare.php @android-arm64-v8a --with-parallel-jobs=4
bash make.sh all-library
bash make.sh config
bash make.sh libphp
GLOBAL_PREFIX="$PWD/var/android-arm64-v8a/deps" \
  bash sapi/scripts/package-php-runtime-layer.sh android-arm64-v8a
```

详见 [docs/iOS.md](docs/iOS.md) · [docs/sdk.md](docs/sdk.md) · [docs/libphp.md](docs/libphp.md)

### 相关文档

- [构建选项](docs/options.md) · [常见问题](docs/FAQ.md)
- [已内置扩展](docs/extensions.md) · [依赖关系图](docs/dependency-graph.md)
- [快速初始化构建环境](sapi/quickstart/README.md)
- [搭建依赖库镜像服务](sapi/download-box/README.md)

## 授权协议

- `swoole-cli` 使用了多个其他开源项目，请认真阅读自动生成的 `bin/LICENSE`
  文件中的版权协议，遵守对应开源项目的 `LICENSE`。
- `swoole-cli` 本身的软件源代码、文档等内容以 **Apache 2.0 LICENSE** +
  **SWOOLE-CLI LICENSE** 作为双重授权协议，用户需要同时遵守两种协议所规定的条款。

### SWOOLE-CLI LICENSE

- 对 `swoole-cli` 代码进行使用、修改、发布的新项目必须含有 `SWOOLE-CLI LICENSE`
  的全部内容。
- 使用 `swoole-cli` 代码重新发布为新项目或产品时，项目或产品名称不得包含
  `swoole` 单词。
