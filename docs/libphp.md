# 构建完全静态的 libphp.a

`swoole-cli` 默认只产出 `bin/swoole-cli` 可执行文件。本文说明如何额外产出一个
**完全静态、自包含的 `libphp.a`**，用于把 PHP 内核 + 全部内置扩展（含 swoole）
嵌入到自己的 C/C++ 程序中。

## 产物

| 路径 | 内容 |
|------|------|
| `libs/libphp.a` | 完全自包含归档 = PHP 目标文件 + 全部第三方静态库 + **musl libc** |

`libs/libphp.a` 是唯一交付物，与 `bin/swoole-cli` 一样静态并入了 musl libc，
链接时**不需要**再指定 `-lcurl -lssl -licui18n -lMagickWand -lm -lpthread …`
等一长串库，因为它们的目标文件已经在归档里了（musl 的 libm/libpthread/libdl
等本就包含在 libc 中）。

## 编译教程

下面是从零到产出 `libs/libphp.a`、并编译一个可运行的嵌入程序的完整流程。
**所有命令都在构建容器内执行**。

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
phpSrcDir : /work/var/php-8.4.14
```

> 如果 `workDir` 显示的是宿主机路径（如 `/home/xxx/swoole-cli`），说明 `make.sh`
> 是按宿主机直编生成的，进容器后会因找不到 `pool/lib/xxx.tar.gz` 而失败。
> 此时重新执行一次 `php prepare.php`（不要带 `--without-docker`）即可。
> 该选项详见 [options.md](options.md#without-docker)。

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

这一步会做四件事，缺一不可：

1. `./buildconf --force` 重新生成 `configure`——**必须重新生成**，否则新增的
   `--enable-embed` 选项不会出现，`libs/libphp.a` 目标也就不会存在
2. `./configure $OPTIONS` 生成 `Makefile`
3. 导出并写入 `ldflags.log`（第三方库 `-L` 搜索路径）与 `libs.log`（`-l` 列表），
   **第 5 步的合并脚本依赖这两个文件**
4. 把 `Makefile` 里的 `-export-dynamic` 替换为 `-all-static`

### 步骤 5：编译 libphp.a

```shell
./make.sh libphp
```

实际执行的动作：

```shell
rm -f libs/libphp.a                    # 强制重新归档，避免残留旧产物被 make 判为 up to date
make -j $(nproc) libs/libphp.a        # 归档 PHP 自身目标文件 -> libs/libphp.a
bash ./sapi/scripts/build-libphp.sh   # 合并第三方静态库 + musl libc -> libs/libphp.a
```

合并依据 `libs.log` 与 `ldflags.log`，使用 `ar` 的 MRI 模式 `ADDLIB` 逐成员拷贝，
因此不会丢失同名的归档成员；最后把 `musl libc.a` 并入，使归档与 `bin/swoole-cli`
一样静态自带 libc。

合成脚本的输出示例：

```
===============================[libphp]===============================
php archive  : /work/libs/libphp.a
global prefix: /usr/local/swoole-cli
musl libc    : /usr/lib/libc.a
lib dirs     : 49
lib names    : 72
merged(.a)   : 68
system libs  : m pthread rt stdc++
----------------------------------------------------------------------
output       : /work/libs/libphp.a
members      : 8919
size         : 261M

[libphp] 校验 embed SAPI 符号：
  php_embed_init      OK
  php_embed_shutdown  OK

[libphp] 校验 musl libc 符号：
  printf (musl libc)  OK
```

最后几行 `OK` 是关键：`php_embed_init` / `php_embed_shutdown` 找得到说明
embed 入口可用；`printf` 找得到说明 musl libc 已并入。任一 `MISSING`
脚本都会以非 0 退出。

### 步骤 6：验证产物

```shell
# 归档成员数与体积
ar t /work/libs/libphp.a | wc -l
ls -lh /work/libs/libphp.a

# embed SAPI 入口符号
nm -g --defined-only /work/libs/libphp.a | grep -E 'php_embed_(init|shutdown)'

# 确认归档里已含第三方库（例如 curl）
nm -g --defined-only /work/libs/libphp.a | grep -w curl_easy_init

# 确认归档里已含 musl libc（抽查几个常见符号）
nm -g --defined-only /work/libs/libphp.a | grep -wE 'printf|malloc|pthread_create'
```

### 步骤 7：编译一个嵌入示例

embed SAPI 的接口与 php-src 完全一致，更多示例见
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

编译链接（libc 与 C++ 运行时都已并入归档，用 `clang` 即可）：

```shell
# 方式一：直接 -static（最简单，clang 自动附加的 -lc / -lstdc++ 无副作用）
clang -static -no-pie -o embed_demo embed_demo.c \
    -I/work -I/work/main -I/work/Zend -I/work/TSRM \
    /work/libs/libphp.a

# 方式二：完全不依赖系统库（可把 libphp.a 拿到没有 musl 静态库的机器上）
clang -static -nodefaultlibs -no-pie -o embed_demo embed_demo.c \
    -I/work -I/work/main -I/work/Zend -I/work/TSRM \
    -Wl,--start-group /work/libs/libphp.a -Wl,--end-group
```

要点：

- `-I/work` 必需：`sapi/embed/php_embed.h` 里的 `#include <main/php.h>` 基于源码根目录；
  `-I/work/Zend` 用于 `<zend_ini.h>`
- `-static -no-pie` 必需，原因见下方"已知限制 1"
- musl 的 `libm` / `libpthread` / `libdl` / `libcrypt` 都并进了 `libc.a`，
  libc 又已并入归档；C++ 运行时（`libstdc++` / `libgcc` / `libgcc_eh` /
  `libgomp`）也已并入。因此不再需要 `-lm -lpthread -lc -lstdc++ -lgomp` 等
- 方式二用 `-nodefaultlibs` 去掉 clang 自动附加的库，归档内的 libc / libstdc++
  成员互相引用，`--start-group` 让链接器循环解析以保证符号闭合
- `sapi/embed/php_embed.h` 会随 `make install` 安装到 `$(prefix)/include/php/sapi/embed/`

验证：

```shell
$ ./embed_demo
hello 8.4.14

$ ldd ./embed_demo
	不是动态可执行文件
```

## 常见问题

### `configure: error: C compiler cannot create executables`

看起来像编译器坏了，实际几乎总是**链接阶段找不到库**。打开 `config.log` 搜
`cannot find -l`，例如：

```
/usr/bin/ld: cannot find -lngtcp2: No such file or directory
```

典型成因是 `/usr/local/swoole-cli` 下残留了**旧版本**的第三方库：旧库编译时的配置
与当前 `make.sh` 不一致，其 `pkg-config` 文件（`.pc`）里声明了 `-lxxx` 却没有对应的
`-L` 路径，于是链接探测失败。

排查与修复：

```shell
# 1. 确认报错的库来自哪个 .pc 文件
grep -rn "lngtcp2" /usr/local/swoole-cli/*/lib/pkgconfig/*.pc

# 2. 看哪些库是旧的（对比 .completed 的时间戳）
ls -la /usr/local/swoole-cli/*/.completed

# 3. 清掉旧库后重新全量编译
./make.sh clean-all-library
./make.sh all-library
./make.sh config
```

### `tar: /home/xxx/swoole-cli/pool/lib/xxx.tar.gz: Cannot open`

`make.sh` 里的路径是宿主机目录，容器内不存在。原因见
[options.md](options.md#without-docker)：重新执行 `php prepare.php`
（不带 `--without-docker`），确认输出 `workDir : /work` 后即可。

### `no suitable Python interpreter found`（libpsl）

libpsl 构建时要用 `src/psl-make-dafsa`（Python 脚本）把 public suffix list
转成静态 C 数组，**仅在编译期需要**，产物是纯 C 的字节数组，运行时不依赖 Python。

alpine 基础镜像已包含 `RUN apk add python3`（见 `sapi/docker/Dockerfile`）。
若在自建环境遇到，安装 python3 即可：

```shell
apk add python3      # alpine
apt install python3  # debian
```

### `libs/libphp.a` 太大 / 想减小体积

归档含 ICU 数据表、ImageMagick、libheif 等，数百 MB 量级属正常。若确定用不到
某些扩展，通过 `php prepare.php -mongodb -imagick ...` 关闭后重新走一遍流程。

## 已知限制

### 1. 必须用 `-static -no-pie` 链接

`make.sh` 里只有 8 个第三方库显式加了 `--with-pic`，其余静态库的目标文件
**不是位置无关代码**。因此 `libs/libphp.a` 只能链接进非 PIE 的静态可执行文件，
不能链接进共享库（`.so`）或 PIE 程序，否则会报
`relocation R_X86_64_32S … can not be used when making a PIE object`。

这与 `bin/swoole-cli` 自身的构建方式一致（`make_build` 使用 `-static -all-static`）。

如果确实需要 PIC 版本，需要把所有第三方库都用 `CFLAGS=-fPIC` 重新编译一遍，
再 `./make.sh clean && ./make.sh build && ./make.sh libphp`。

### 2. libc 与 C++ 运行时均已内置

归档包含 `/usr/local/swoole-cli/*/lib/` 下的第三方静态库、**musl libc**，以及
**C++ 运行时**（`libstdc++`、`libgcc`、`libgcc_eh`、`libgomp`）。

- musl 的 `libm` / `libpthread` / `libdl` / `libcrypt` / `libresolv` / `librt`
  本就并入 libc，因此一并进入归档
- PHP 内核含 C++ 对象（ICU、ImageMagick 等），其 `operator new/delete`、
  `std::string` 等符号来自 `libstdc++.a`。注意 swoole-cli 用 `clang++` 编译且
  未指定 `-stdlib`，因此是 **libstdc++ ABI**（不是 libc++），不可混用

因此链接时不再需要任何额外的 `-l` 库；用 `-nodefaultlibs` 时也只需
`libphp.a` 一个归档（配合 `--start-group`）。

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

`libs/libphp.a` 通常数百 MB（ICU 数据表、ImageMagick、libheif、musl libc 等占大头）。
`libs/` 已被 `.gitignore` 忽略，不会进入版本库。

## 实现说明

- `sapi/embed/` —— 从 php-src 恢复的 embed SAPI 源码。
  `config.m4` 经过改写：**不走** `PHP_SELECT_SAPI`，因为 swoole-cli 的
  `configure.ac` 硬编码了 `PHP_SAPI=none`，`PHP_SELECT_SAPI` 会覆盖
  `PHP_SAPI` / `OVERALL_TARGET` / `install_sapi`。改为只把 `php_embed.c`
  编译进独立的 `PHP_EMBED_OBJS`，因此对 `bin/swoole-cli` 的构建零影响。
  `sync-source-code.php` 只同步 `php_embed.c/h`，不会覆盖改写的 `config.m4`。
- `sapi/embed/Makefile.frag` —— `libs/libphp.a` 目标。除 PHP 自身对象外，
  还并入了 swoole-cli 对内核的 patch/hook 对象（`sapi/cli/patch.c`、
  `sapi/cli/sfx/hook_phar.c`、`sapi/cli/sfx/sfx.c`），这些虽在 `sapi/cli` 下，
  却是 `ext/phar`、`ext/zip` 等内核对象硬依赖的（`opcache_module_entry`、
  `hook_plain_stream_*` 等符号）
- `sapi/embed/php_embed_stub.c` —— 桩实现。`ext/readline/readline_cli.c`
  引用了含 `main()` 的 `php_cli.c` 里的 `php_cli_get_shell_callbacks()`，
  而 `readline_cli.c` 又是 readline 扩展的一部分（`readline.c` 引用它的
  MINIT/MSHUTDOWN/MINFO）无法排除，故在 embed 侧提供该函数的桩（返回 NULL，
  让 readline_cli 跳过 CLI shell 回调注册，不影响 readline 核心功能）
- `sapi/scripts/build-libphp.sh` —— 归档合成（第三方静态库 + musl libc）
- `sapi/src/template/make.php` —— `make_libphp()` 与 `./make.sh libphp` 入口
  （改模板后需重新生成 `make.sh`）
