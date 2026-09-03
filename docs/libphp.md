# 构建完全静态的 libphp.a

`swoole-cli` 默认只产出 `bin/swoole-cli` 可执行文件。本文说明如何额外产出一个
**完全静态、自包含的 `libphp.a`**，用于把 PHP 内核 + 全部内置扩展（含 swoole）
嵌入到自己的 C/C++ 程序中。

## 产物

| 路径 | 内容 |
|------|------|
| `libs/libphp-core.a` | 纯 PHP 目标文件归档：PHP 内核 + Zend + TSRM + 全部内置扩展 + embed SAPI |
| `libs/libphp.a` | 自包含胖归档 = `libphp-core.a` + swoole-cli 自行编译的全部第三方静态库 |

`libs/libphp.a` 是最终交付物。链接时**不需要**再指定
`-lcurl -lssl -licui18n -lMagickWand …` 等一长串第三方库，因为它们的目标文件
已经在归档里了。

## 构建

在构建容器中执行：

```shell
./make.sh all-library   # 第三方库尚未编译时先执行
./make.sh config
./make.sh libphp
```

`./make.sh libphp` 实际做了三件事：

```shell
make -j $(nproc) libs/libphp.a        # 1. 归档 PHP 自己的目标文件
mv -f libs/libphp.a libs/libphp-core.a
bash ./sapi/scripts/build-libphp.sh   # 2. 合并第三方静态库 -> libs/libphp.a
```

合并依据 `libs.log`（第三方库 `-l` 列表）和 `ldflags.log`（`-L` 搜索路径），
二者由 `./make.sh config` 写入工作目录。合并使用 `ar` 的 MRI 模式 `ADDLIB`
逐成员拷贝，因此不会丢失同名的归档成员。

合成脚本的输出示例：

```
===============================[libphp]===============================
core archive : /work/libs/libphp-core.a
global prefix: /usr/local/swoole-cli
lib dirs     : 36
lib names    : 58
merged(.a)   : 52
system libs  : m pthread stdc++ intl ltdl
----------------------------------------------------------------------
output       : /work/libs/libphp.a
members      : 33128
size         : 1.2G
```

## 使用

### 1. 嵌入代码

embed SAPI 的接口与 php-src 完全一致，示例见
[`sapi/embed/README.md`](../sapi/embed/README.md)。最小示例：

```c
/* embed_demo.c */
#include <sapi/embed/php_embed.h>

int main(int argc, char **argv)
{
	PHP_EMBED_START_BLOCK(argc, argv)
		zend_eval_stringl(ZEND_STRL("echo 'hello ', PHP_VERSION, PHP_EOL;"), NULL, "demo");
	PHP_EMBED_END_BLOCK()
	return 0;
}
```

### 2. 编译与链接

```shell
clang -static -no-pie -o embed_demo embed_demo.c \
    -I/work -I/work/main -I/work/Zend -I/work/TSRM \
    /work/libs/libphp.a \
    -lstdc++ -lgomp -lcrypt -ldl -lm -lpthread
```

要点：

- `-I/work` 必需：`sapi/embed/php_embed.h` 里的 `#include <main/php.h>` 基于源码根目录；
  `-I/work/Zend` 用于 `<zend_ini.h>`
- `-static -no-pie` 必需，原因见下方"已知限制"
- `sapi/embed/php_embed.h` 会随 `make install` 安装到 `$(prefix)/include/php/sapi/embed/`

### 3. 验证静态性

```shell
$ ldd ./embed_demo
	不是动态可执行文件
```

## 已知限制

### 1. 必须用 `-static -no-pie` 链接

`make.sh` 里只有 8 个第三方库显式加了 `--with-pic`，其余静态库的目标文件
**不是位置无关代码**。因此 `libs/libphp.a` 只能链接进非 PIE 的静态可执行文件，
不能链接进共享库（`.so`）或 PIE 程序，否则会报
`relocation R_X86_64_32S … can not be used when making a PIE object`。

这与 `bin/swoole-cli` 自身的构建方式一致（`make_build` 使用 `-static -all-static`）。

如果确实需要 PIC 版本，需要把所有第三方库都用 `CFLAGS=-fPIC` 重新编译一遍，
再 `./make.sh clean && ./make.sh build && ./make.sh libphp`。

### 2. 系统库不在归档里

归档只包含 `/usr/local/swoole-cli/*/lib/` 下由 swoole-cli 自行编译的静态库。
以下系统库仍由工具链在 `-static` 时提供，合成脚本会把它们打印在
`system libs` 一行：

`libc` `libm` `libpthread` `libdl` `libstdc++` `libgomp` `libcrypt` `libresolv` `libintl` `libltdl`

### 3. 只有 musl（alpine）容器下才是真正"不依赖系统 so"

`make.sh docker-build` 使用 `alpine:3.18` 作为基础镜像，glibc 环境下做全静态链接
会在 `getaddrinfo` / `dlopen` 等处产生告警，且 NSS 相关功能受限。
若要得到可移植的完全静态产物，请在 alpine 容器中构建。

### 4. embed SAPI 不提供 CLI 的命令行能力

`libs/libphp.a` 只包含 embed SAPI（`php_embed_init` / `php_embed_shutdown`），
**不包含** `bin/swoole-cli` 的：

- 命令行参数解析（`-r` `-a` `-f` `-l` 等）
- SFX 自解压、`swoole-cli` 自更新
- fpm（`fpm_main`）

这些能力在 `sapi/cli` 中，且 `sapi/cli/php_cli.c` 含有 `main()`，
不适合打进库里。需要的话请在宿主程序中自行实现入口逻辑。

### 5. phpy 需要静态 libpython

若启用 `--enable-phpy`，链接需要静态 `libpython`。alpine 基础镜像未安装
`python3-dev`，默认配置下 phpy 是关闭的，因此默认路径不受影响。

### 6. 产物体积

`libs/libphp.a` 通常在 GB 量级（ICU 数据表、ImageMagick、libheif 等占大头）。
`libs/` 已被 `.gitignore` 忽略，不会进入版本库。

## 实现说明

- `sapi/embed/` —— 从 php-src 恢复的 embed SAPI 源码。
  `config.m4` 经过改写：**不走** `PHP_SELECT_SAPI`，因为 swoole-cli 的
  `configure.ac` 硬编码了 `PHP_SAPI=none`，`PHP_SELECT_SAPI` 会覆盖
  `PHP_SAPI` / `OVERALL_TARGET` / `install_sapi`。改为只把 `php_embed.c`
  编译进独立的 `PHP_EMBED_OBJS`，因此对 `bin/swoole-cli` 的构建零影响。
  `sync-source-code.php` 只同步 `php_embed.c/h`，不会覆盖改写的 `config.m4`。
- `sapi/embed/Makefile.frag` —— `libs/libphp.a` 目标
- `sapi/scripts/build-libphp.sh` —— 胖归档合成
- `sapi/src/template/make.php` —— `make_libphp()` 与 `./make.sh libphp` 入口
  （改模板后需重新生成 `make.sh`）
