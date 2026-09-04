# 构建与使用 swoole-cli-sdk

`swoole-cli` 默认只产出 `bin/swoole-cli` 可执行文件。SDK 是在此基础上，
把**完全静态、自包含的静态库**与**全部头文件**打包成一个压缩包，分发给你，
用于在自己的 C/C++ 程序中嵌入 PHP 内核 + 全部内置扩展（含 swoole）。

- `libphp.a` —— PHP 内核 + Zend + TSRM + 全部扩展 + embed SAPI + 第三方库 + musl libc + C++ 运行时
- `libphpx.a` —— [php-x](https://github.com/swoole/phpx) C++ 封装层（依赖 `libphp.a` 提供 gmp/gmpxx/mpfr 符号）

> 若只需要 `libphp.a` 而无需打包分发，请直接看 [libphp.md](libphp.md)。

## 目录

- [SDK 内容](#sdk-内容)
- [一、构建 SDK](#一构建-sdk)
- [二、使用 SDK](#二使用-sdk)
- [三、链接要点](#三链接要点)
- [四、常见问题](#四常见问题)

---

## SDK 内容

`./make.sh sdk` 产出一个 tar.xz，命名规则：

```
swoole-cli-sdk-v<swoole版本>-php<php版本>-<os>-<arch>.tar.xz
# 例：swoole-cli-sdk-v6.2.2-php8.4.25-linux-x64.tar.xz
```

解压后的目录结构：

```
swoole-cli-sdk-v6.2.2-php8.4.25-linux-x64/
├── include/
│   ├── <第三方库头文件，平铺到根>   # openssl/ curl/ gmp.h mpfr.h …
│   ├── php/                        # PHP 内核头：main/ Zend/ TSRM/ ext/ sapi/embed/
│   └── phpx/                       # phpx 头：phpx.h phpx_func.h phpx_decimal.h …
│                                   # 另含 phpx 依赖的第三方头：
│                                   # decimal.hh、mpdecimal.h
└── lib/
    ├── libphp.a                    # 自包含：PHP + 扩展 + 第三方库 + musl libc + C++ 运行时
    ├── libphpx.a                   # phpx（mpdecimal/wren_gc 已内置，gmp/gmpxx/mpfr 由 libphp.a 提供）
    └── musl/                       # musl 启动文件 crt1.o crti.o crtn.o rcrt1.o Scrt1.o
```

要点：

- 第三方库头文件**平铺在 `include/` 根**，`-I {SDK}/include` 后
  `#include <openssl/ssl.h>`、`#include <curl/curl.h>` 等用法与 `/usr/include` 完全一致
- PHP 内核头在 `include/php/` 下保持 `main/ Zend/ TSRM/ ext/ sapi/embed/` 目录结构，
  `-I {SDK}/include/php` 后 `#include <main/php.h>`、`#include <sapi/embed/php_embed.h>` 可用
- phpx 依赖的头（`decimal.hh`、`mpdecimal.h`）放在 `include/phpx/` 下
  与 phpx 自身头文件同目录——`phpx_decimal.h` 属于 phpx 公开 API，
  其 `#include <decimal.hh>` 靠 `-I {SDK}/include/phpx` 命中
- `lib/musl/` 里是 musl 的启动文件：`libphp.a` 内嵌的是 **musl libc**，
  最终可执行文件必须用 musl 的 C 运行时（而不是目标机 glibc 的）链接

---

## 一、构建 SDK

从零到产出 `sdk/*.tar.xz` 的完整流程。**所有命令都在构建容器内执行**。

### 步骤 1：准备构建容器

```shell
./make.sh docker-build    # 构建基础镜像，只需执行一次
./make.sh docker-bash     # 启动并进入容器
```

基础镜像为 `alpine:3.18`（musl libc），只有 musl 环境才能产出真正不依赖系统
`.so` 的静态产物。

### 步骤 2：生成构建脚本 `make.sh`

```shell
php prepare.php
```

Linux 下**默认按容器内构建**生成，输出应为：

```
build in container : yes
workDir   : /work
buildDir  : /work/thirdparty
phpSrcDir : /work/var/php-8.4.25
```

> 如果 `workDir` 显示的是宿主机路径（如 `/home/xxx/swoole-cli`），说明 `make.sh`
> 是按宿主机直编生成的，进容器后会因找不到 `pool/lib/xxx.tar.gz` 而失败。
> 此时重新执行一次 `php prepare.php`（不要带 `--without-docker`）即可。
> 详见 [options.md](options.md#without-docker)。

### 步骤 3：编译第三方依赖库

```shell
./make.sh all-library
```

48 个第三方库，首次全量编译约 1–2 小时。每个库编译完成后会在
`/usr/local/swoole-cli/<库名>/.completed` 留下标记，重跑时会跳过。

### 步骤 4：生成 `configure` 与 `Makefile`

```shell
./make.sh config
```

### 步骤 5：编译 `libphp.a`

```shell
./make.sh libphp
```

产出 `/work/libs/libphp.a`（完全自包含归档）。这一步的细节、校验与常见问题
见 [libphp.md](libphp.md)（步骤 4–7）。

### 步骤 6：编译 `libphpx.a`（可选）

```shell
./make.sh phpx
```

产出 `/work/libs/libphpx.a`。phpx 源码位于 `/work/thirdparty/phpx`，由
`php prepare.php` 阶段按 `sapi/PHPX-VERSION.conf` 自动下载（`master` 用
`git clone`/`git pull`，固定版本用 GitHub tag 归档）。

> 不用 phpx（纯 C 嵌入）可跳过本步骤；但 `./make.sh sdk` 要求 `libphpx.a`
> 存在，若跳过则需临时屏蔽 build-sdk.sh 里的校验。

### 步骤 7：打包 SDK

```shell
./make.sh sdk
```

产出 `/work/sdk/swoole-cli-sdk-v<swoole>-php<php>-linux-<arch>.tar.xz`，
并把第三方库头文件平铺进 `include/`、PHP 头收集到 `include/php/`、phpx 头
收集到 `include/phpx/`、musl 启动文件收集到 `lib/musl/`。

---

## 二、使用 SDK

以下示例均在**解压后的 SDK 目录**（记作 `$SDK`）下进行：

```shell
tar -xJf swoole-cli-sdk-v6.2.2-php8.4.25-linux-x64.tar.xz
SDK="$(pwd)/swoole-cli-sdk-v6.2.2-php8.4.25-linux-x64"
```

### 编译 embed 程序（纯 C）

用 embed SAPI 启动 Zend 引擎并执行一段 PHP 代码。接口与 php-src 完全一致，
更多示例见 [`sapi/embed/README.md`](../sapi/embed/README.md)。

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

编译链接：

```shell
clang -static -Wl,-no-pie -o embed_demo embed_demo.c \
    -I"$SDK/include/php" \
    -I"$SDK/include/php/main" -I"$SDK/include/php/Zend" -I"$SDK/include/php/TSRM" \
    "$SDK/lib/libphp.a"
```

运行：

```shell
$ ./embed_demo
hello 8.4.25
```

### 编译 phpx 程序（C++）

用 php-x 的 C++ API 操作 PHP 变量、调用函数：

```cpp
/* phpx_demo.cc */
#include "sapi/embed/php_embed.h"
#include "phpx.h"

using namespace php;

int main(int argc, char *argv[])
{
	php_embed_init(argc, argv);

	echo("hello phpx\n");

	Variant a = 1;
	Variant b = 2;
	echo("sum=%ld\n", (a + b).toInt());

	php_embed_shutdown();
	return 0;
}
```

编译链接（用 `clang++`，且 `libphpx.a` 放在 `libphp.a` **前面**）：

```shell
clang++ -static -Wl,-no-pie -o phpx_demo phpx_demo.cc \
    -I"$SDK/include/phpx" \
    -I"$SDK/include/php" -I"$SDK/include/php/main" -I"$SDK/include/php/Zend" \
    -I"$SDK/include/php/TSRM" -I"$SDK/include/php/ext" -I"$SDK/include/php/ext/date/lib" \
    -Wl,--start-group "$SDK/lib/libphpx.a" "$SDK/lib/libphp.a" -Wl,--end-group
```

运行：

```shell
$ ./phpx_demo
hello phpx
sum=3
```

### 用 phpx 的 Decimal（依赖 decimal.hh）

`phpx_decimal.h` 提供高精度十进制计算，其内部的 `#include <decimal.hh>`
由 SDK 的 `include/phpx/decimal.hh` 提供：

```cpp
/* decimal_demo.cc */
#include "sapi/embed/php_embed.h"
#include "phpx.h"
#include "phpx_decimal.h"

using namespace php;

int main(int argc, char *argv[])
{
	php_embed_init(argc, argv);

	Variant a = toDecimal(String("123.456"));
	Variant b = toDecimal(String("0.544"));
	Variant sum = Decimal::add(a, b);
	echo("a+b=%s\n", Decimal::toString(sum).toCString());

	php_embed_shutdown();
	return 0;
}
```

编译命令与上面的 phpx 示例完全一致（只是源文件换成 `decimal_demo.cc`）。
运行：

```shell
$ ./decimal_demo
a+b=124.000
```

能得到精确结果，说明 `libphpx.a` 里内置的 mpdecimal 符号工作正常。

---

## 三、链接要点

### 1. 必须在 musl（alpine）环境链接

`libphp.a` 内嵌 musl libc，因此**推荐在 musl 环境（如 alpine 容器）里编译链接**，
clang/gcc 会自动选用 musl 的启动文件与 libc，命令最简：

```shell
clang -static -Wl,-no-pie -o demo demo.c -I"$SDK/include/php" "$SDK/lib/libphp.a"
```

这正是上面 embed/phpx 两个示例所处的环境。swoole-cli 自身的 `bin/swoole-cli`
也是这么构建的。

### 2. musl 启动文件（`lib/musl/`）的作用

若在 **glibc 主机**上链接，编译器默认选用 glibc 的 `crt1.o`。而 `libphp.a`
里 musl 的 `__libc_start_main` 会安装 musl 的线程指针布局，与 glibc `crt1.o`
的 `_start`（按 glibc 约定）不匹配，PHP ZTS 的 `__thread` 全局会读到错误地址，
进程在启动阶段崩溃。

因此 SDK 打包了 musl 的启动文件到 `lib/musl/`。但注意：glibc 下完全静态链接
还涉及 `crtbegin.o`/`crtend.o`（提供 C++ 的 `__dso_handle`）、`libgcc` 等，
仅靠 `lib/musl/` 这 5 个 `.o` 无法闭环（会报 `undefined reference to __dso_handle`）。

**结论**：跨 glibc 链接请自备完整 musl 工具链（`musl-cross-make` 或直接使用
alpine 容器），不要混用 glibc 的 crt/crtbegin。SDK 的 `lib/musl/` 主要是给
musl 工具链 / 容器内手动指定启动文件使用的。

### 3. `-static -Wl,-no-pie` 必需

第三方静态库的目标文件**不是位置无关代码**（PIC），只能链接进非 PIE 的静态
可执行文件。原因与 `bin/swoole-cli` 一致，详见 [libphp.md](libphp.md) 的
"已知限制 1"。

### 4. 无需再指定任何 `-l` 库

musl 的 `libm`/`libpthread`/`libdl` 等已并入 `libc.a`，`libc.a` 与 C++ 运行时
（`libstdc++`/`libgcc`/`libgcc_eh`/`libgomp`）都已并入 `libphp.a`。因此链接时
不需要 `-lm -lpthread -lc -lstdc++` 等，只需一个 `libphp.a`（用 phpx 时再加
`libphpx.a`，用 `--start-group` 包裹）。

### 5. include 路径

- 纯 C embed：`-I"$SDK/include/php"` + `main` `Zend` `TSRM` 三个子目录
- phpx（C++）：在 embed 基础上再加 `-I"$SDK/include/phpx"` + `ext` `ext/date/lib`
- 用到第三方库头（如 `curl/curl.h`）：再加 `-I"$SDK/include"`

---

## 四、常见问题

### `configure: error: C compiler cannot create executables`

第三方库残留旧版本所致，排查方法见 [libphp.md](libphp.md) 的对应章节。

### `undefined reference to __dso_handle`

在 glibc 主机上链接时出现。根因是 glibc 的 `crtbegin.o` 没被正确引入，见
上文"链接要点 2"。解决办法：换到 musl（alpine）环境链接。

### `relocation R_X86_64_32S … can not be used when making a PIE object`

漏了 `-Wl,-no-pie` 或用了 `-fPIE`。静态库非 PIC，必须链接成非 PIE 静态程序。

### 产物体积

`libphp.a` 通常数百 MB（ICU 数据表、ImageMagick、libheif、musl libc 等占大头），
链接出的可执行文件也会偏大，属正常现象。

---

## 实现说明

- `sapi/scripts/build-libphp.sh` —— 合成 `libs/libphp.a`（第三方库 + musl libc + C++ 运行时）
- `sapi/scripts/build-phpx.sh` —— 用 phpx 仓库的 `full-static/` 目录编译 `libs/libphpx.a`
- `sapi/scripts/build-sdk.sh` —— 打包 SDK（静态库 + 头文件 + musl 启动文件）
- `sapi/src/template/make.php` —— `make_libphp()` / `make_phpx()` / `make_sdk()` 入口
  （改模板后需重新生成 `make.sh`）
- `libphpx.a` 已内置 mpdecimal / wren_gc（通过 OBJECT library 并入），
  gmp / gmpxx / mpfr 符号则由 `libphp.a` 提供，链接时两者需同时出现
