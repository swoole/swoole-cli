# Windows 下 PHP 静态编译指南

本文档记录在 Windows 上使用 MSVC 静态编译 PHP 8.4 的完整流程，产物为单文件 `php.exe`，无 `php_*.dll`、无 `VCRUNTIME140.dll` 依赖。

## 环境要求

- PHP 8.4.x 源码
- Visual Studio 2022（含 C++ 工具链）
- PHP SDK Binary Tools：`https://github.com/php/php-sdk-binary-tools.git`
  - 其 `bin` 目录提供 `bison.exe`、`re2c.exe`、`sed.exe` 等构建工具，无需额外安装 MSYS2 / Git Bash
- 依赖库的静态构建产物（zlib、openssl、curl、libxml2、iconv 等）

## 目录约定

```
<project>/
├── build/                      # 依赖库静态构建产物
│   ├── zlib/{include,lib}
│   ├── openssl/{include,lib}
│   ├── curl/{include,lib}
│   └── ...
├── var/native-build/
│   ├── php-src/                # PHP 源码
│   └── php-sdk-binary-tools/   # php-sdk 工具
└── sapi/scripts/windows-native/
├── main.bat                # 调度入口
└── build.bat               # 实际构建逻辑
```

## 构建流程总览

```
0. 入口 phpsdk-starter.bat → main.bat → build.bat
1. 环境准备
2. 注入依赖库路径（INCLUDE / LIB）
3. 构建配置层修改（config.w32 / confutils.js）→ buildconf.bat -f
4. 工具链 flag（CFLAGS / LDFLAGS）
5. 源码适配补丁（sed + bison）
6. configure.bat
7. 去除 Makefile 中的 DLL 构建残留
8. 追加静态链接目标到 Makefile
9. nmake /E
10. 验证
```

---

## 0. 入口

整个构建由 `phpsdk-starter.bat` 启动，它负责初始化 VS 环境、把 php-sdk 的 `bin` 目录（`bison` / `re2c` / `sed`）加入 `PATH`、设置 `PHP_SDK_*` 变量，然后调用目标脚本：

```bat
var\native-build\php-sdk-binary-tools\phpsdk-starter.bat ^
    -c vs17 ^
    -a x64 ^
    -t sapi\scripts\windows-native\main.bat
```

| 参数 | 作用 |
|---|---|
| `-c vs17` | 选择 Visual Studio 2022（VS 17）工具链 |
| `-a x64` | 目标架构 x64 |
| `-t sapi\scripts\windows-native\main.bat` | 目标脚本，内部调用 `build.bat` |

调用链：

```
phpsdk-starter.bat
  → 初始化 VS 环境 + php-sdk bin（bison/re2c/sed）
  → sapi\scripts\windows-native\main.bat
  → build.bat（本文档第 1~9 节）
```

---

## 1. 环境准备

`build.bat` 被 starter 调用时，VS 环境和 php-sdk 工具已在 `PATH` 中。脚本开头的 `vswhere` / `where bison` 主要用于**输出诊断信息**，确认环境正确：

```bat
@echo off
setlocal
cd /d %~dp0
cd /d ..\..\..\

vswhere -products * -latest -prerelease -find **\VC\Auxiliary\Build\vcvarsall.bat
env

rem bison / re2c / sed 由 php-sdk-binary-tools 的 bin 目录提供
where bison
bison --version
re2c --version

set "__PROJECT__=%cd%"
cd /d %__PROJECT__%\var\native-build\php-src\
set "PHP_SRC=%cd%"

if exist "configure.js" (
    nmake clean
)
```

---

## 2. 注入依赖库路径

将各静态库的 `include` 和 `lib` 目录追加到 `INCLUDE` / `LIB`：

```bat
set "X_INCLUDES="
set "X_LIBS="

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\zlib\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\zlib\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\openssl\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\openssl\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\liblz4\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\liblz4\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\bzip2\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\bzip2\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\liblzma\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\liblzma\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\brotli\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\brotli\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libzstd\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libzstd\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libzip\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libzip\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libiconv\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libiconv\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libpsl\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libpsl\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\cares\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\cares\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libssh2\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libssh2\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libnghttp2\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libnghttp2\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libnghttp3\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libnghttp3\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\ngtcp2\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\ngtcp2\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\oniguruma\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\oniguruma\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\lib"

set "INCLUDE=%X_INCLUDES%;%INCLUDE%"
set "LIB=%X_LIBS%;%LIB%"

set "X_CUSTOM_LIBS="
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% libzstd_a.lib"
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% cares.lib psl.lib ngtcp2.lib ngtcp2_crypto_ossl.lib nghttp3.lib "
```

> `X_CUSTOM_LIBS` 用于补充 configure 未自动推导的库，会在第 8 节的链接目标中注入。

---

## 3. 构建配置层修改（**必须在 `buildconf.bat -f` 之前**）

### 3.1 强制扩展静态编译

将 `ext/*/config.w32` 中 `EXTENSION()` 的第三个参数从 `true` 改为布尔 `false`：

```bat
rem fileinfo
sed.exe -i "s/, true,/, false,/" ext\fileinfo\config.w32
```

> **注意**
>
> - 必须是布尔 `false`，不能是字符串 `"false"`，也不能是 `null`。
> - `null` 会回退到 `PHP_XXX_SHARED`，结果取决于配置阶段变量，不确定。
> - `EXTENSION()` 内部逻辑：`shared == null` 时走 `force_all_shared()` 或 `shared = PHP_XXX_SHARED`；`shared` 非空时直接使用传入值。传 `false` 是最确定的静态路径。
> - 其他需要静态编译的扩展同理。

### 3.2 禁用全局强制 shared（可选但推荐）

修改源头 `win32/build/confutils.js`，让 `force_all_shared()` 永远返回 `false`，防止 `--enable-all-shared` / `--with-all-shared` 覆盖单个扩展配置：

```bat
sed.exe -i "s/!!PHP_ALL_SHARED && \"yes\" == PHP_ALL_SHARED/false/" win32\build\confutils.js
```

### 3.3 重新生成 `configure.js`

```bat
call buildconf.bat -f
```

> `configure.js` 是生成文件，**所有对 `config.w32` / `confutils.js` 的修改必须在这一步之前完成**。`-f` 强制重新生成，缺少它可能导致使用旧的 `configure.js`，修改不生效。

---

## 4. 工具链 flag（编译期 + 链接期）

```bat
set "CFLAGS=/EHsc /MP /MT /UCRT"

set "LDFLAGS=/VERBOSE:LIB ^
 /DEFAULTLIB:libvcruntime.lib ^
 /DEFAULTLIB:libucrt.lib ^
 /DEFAULTLIB:onecore.lib ^
 /NODEFAULTLIB:MSVCRT ^
 /NODEFAULTLIB:kernel32.lib"
```

| Flag | 作用 |
|---|---|
| `/MT` | 编译期选定静态 CRT（`LIBCMT.lib`），脱离 `msvcrt.dll` 导入模型 |
| `/UCRT` | UCRT 静态链接模式下宏路径一致 |
| `/EHsc` | C++ 异常模型 |
| `/MP` | 多进程编译 |
| `/NODEFAULTLIB:MSVCRT` | 切断动态 CRT 回流 |
| `/DEFAULTLIB:libucrt.lib` | 显式绑定静态 UCRT |
| `/DEFAULTLIB:libvcruntime.lib` | 显式绑定静态 VCRuntime |
| `/NODEFAULTLIB:kernel32.lib` | 切断 `MSVCRT.lib` 隐藏的 kernel32 引用 |
| `/DEFAULTLIB:onecore.lib` | 用 OneCore umbrella 收敛 Win32 API 解析 |

---

## 5. 源码适配补丁（`buildconf` 之后、`configure` 之前）

```bat
rem 1. 禁用 vcruntime140.dll 运行时版本检查（/MT 下必然失败）
if not exist "win32\winutil.c.bak" (
    sed.exe -i".bak" "s/#if PHP_LINKER_MAJOR == 14/#if 0/" win32\winutil.c
)

rem 2. POSIX isatty → MSVC CRT 的 _isatty
set "CFLAGS=%CFLAGS% -D isatty=_isatty"
sed.exe -i.bak 's/ZEND_DLIMPORT/ /' Zend\zend_stream.c

rem 3. ZTS 静态 CLI 移除独立 TSRMLS 缓存
sed.exe -i.bak 's/ZEND_TSRMLS_CACHE_DEFINE()/ /' sapi/cli/php_cli.c

rem 4. 禁用 dllmain.c 中 libxml 的 DLL 生命周期钩子
sed.exe -i.bak 's/#ifdef HAVE_LIBXML/#ifdef HAVE_LIBXML_X/' win32/dllmain.c

rem 5. 重新生成 parser，适配 MSVC 工具链
bison.exe -Wall --output=Zend/zend_language_parser.c -v -d Zend/zend_language_parser.y
```

| 补丁 | 解决的冲突 |
|---|---|
| `winutil.c`: `PHP_LINKER_MAJOR == 14` → `0` | `/MT` 下 `vcruntime140.dll` 版本检查必然失败，禁用该检查 |
| `-D isatty=_isatty` | MSVC CRT 实际符号是 `_isatty`，否则 `LNK2019` |
| `zend_stream.c`: 移除 `ZEND_DLIMPORT` | 配合上一条，`__declspec(dllimport)` 修饰静态 CRT 符号会引发导入查找 |
| `php_cli.c`: 移除 `ZEND_TSRMLS_CACHE_DEFINE()` | ZTS 静态 CLI 镜像持有独立 TSRMLS 缓存，非主线程启动请求时可能崩溃 |
| `dllmain.c`: `HAVE_LIBXML` → `HAVE_LIBXML_X` | 禁用 DLL 生命周期里的 libxml 线程初始化钩子，静态 libxml 不需要 |
| `bison` 重新生成 parser | 让解析器代码与当前 MSVC 工具链完全适配 |

---

## 6. 配置

```bat
set CL=/MP

configure.bat ^
--with-php-build="%PHP_PHP_BUILD%" ^
--with-extra-includes="%INCLUDE%" ^
--with-extra-libs="%LIB%" ^
--with-toolset=vs ^
--with-mp=auto ^
--disable-all ^
--disable-cgi ^
--enable-cli ^
--enable-zts ^
--enable-filter ^
--enable-session ^
--enable-tokenizer ^
--enable-ctype ^
--enable-pdo ^
--enable-phar ^
--enable-phar-native-ssl ^
--enable-fileinfo ^
--enable-sockets ^
--enable-bcmath ^
--enable-zlib ^
--with-openssl=static ^
--with-bz2 ^
--enable-zip ^
--with-iconv=static ^
--with-libxml ^
--with-xml ^
--enable-xmlreader ^
--enable-xmlwriter ^
--with-dom ^
--with-simplexml ^
--with-curl ^
--enable-mbstring ^
--enable-mbregex ^
--enable-exif
```

配置成功后会输出扩展汇总，所有扩展的 Mode 应为 `static`：

```
Enabled extensions:
-----------------------
| Extension  | Mode   |
-----------------------
| ctype      | static |
| date       | static |
| fileinfo   | static |
| filter     | static |
| hash       | static |
| json       | static |
| pcre       | static |
| pdo        | static |
| phar       | static |
| random     | static |
| reflection | static |
| session    | static |
| spl        | static |
| standard   | static |
| tokenizer  | static |
-----------------------
```

> 无 `[shared]` 后缀即表示静态。

---

## 7. 去除 Makefile 中的 DLL 构建残留（`configure.bat` 之后）

`configure.bat` 生成的 `Makefile` 默认按“PHP 主体为 DLL + CLI 为 EXE”的模型编写，静态构建需要先拆掉这些 DLL 专用设置：

```bat
rem 去掉 /dll 链接标志
sed.exe -i "s/DLL_LDFLAGS=\/dll /DLL_LDFLAGS=/" %X_MAKEFILE%

rem 清空 PHP_LDFLAGS 中的 DLL 成分（/dll、排除 libcmt、.def 导出）
sed.exe -i "s/PHP_LDFLAGS=\$(DLL_LDFLAGS) \/nodefaultlib:libcmt \/def:\$(PHPDEF) /PHP_LDFLAGS=/" %X_MAKEFILE%

rem /LD /MD → /MT（不再生成 DLL，CRT 走静态）
sed.exe -i "s/\/LD \/MD/\/MT/" %X_MAKEFILE%

rem 去掉 _USRDLL 宏（避免 dllexport/dllimport 修饰）
sed.exe -i "s/\/D _USRDLL/ /" %X_MAKEFILE%
```

| 替换 | 作用 |
|---|---|
| `DLL_LDFLAGS=/dll` → 空 | 不生成 DLL，生成 EXE |
| `PHP_LDFLAGS=$(DLL_LDFLAGS) /nodefaultlib:libcmt /def:$(PHPDEF)` → 空 | 去掉 `/dll`、**排除 `libcmt`（会抹掉 `/MT` 成果）**、去掉 `.def` 导出 |
| `/LD /MD` → `/MT` | 去掉 DLL 驱动标志，CRT 改为静态 |
| `/D _USRDLL` → 空 | 去掉 DLL 编译宏，避免 `__imp_*` 导入引用 |

> 这四条必须在 `configure.bat` 生成 `Makefile` 之后、`nmake` 之前执行。如果走 `.bat` 而非 sh，把单引号改为双引号（cmd 不认单引号）。

---

## 8. 追加静态链接目标到 Makefile

在 `Makefile` 末尾追加自定义链接目标，显式控制对象列表、库列表、`LDFLAGS` 和自定义库：

```bat
echo x-release-static-php^: generated_files $(DEPS_CLI) $(PHP_GLOBAL_OBJS) $(CLI_GLOBAL_OBJS) $(STATIC_EXT_OBJS)  $(ASM_OBJS)  $(BUILD_DIR)\php.exe.res $(BUILD_DIR)\php.exe.manifest   >> %X_MAKEFILE%
echo 	@"$(LINK)" /nologo  $(STATIC_EXT_OBJS_RESP) $(PHP_GLOBAL_OBJS_RESP) $(CLI_GLOBAL_OBJS_RESP) $(STATIC_EXT_LIBS) $(ASM_OBJS) $(LIBS) $(LIBS_CLI) $(BUILD_DIR)\php.exe.res /out:$(BUILD_DIR)\php.exe $(LDFLAGS) $(LDFLAGS_CLI) $(X_CUSTOM_LIBS) >> %X_MAKEFILE%
rem echo 	-@$(_VC_MANIFEST_EMBED_EXE) >> %X_MAKEFILE%
echo 	@if exist php.exe.manifest $(MT) -nologo -manifest php.exe.manifest -outputresource:php.exe    >> %X_MAKEFILE%
```

其中 `%X_MAKEFILE%` 指向生成的 `Makefile` 路径（如 `var\native-build\php-src\Makefile`）。

**这个 target 的作用：**

| 组成部分 | 作用 |
|---|---|
| `$(STATIC_EXT_OBJS)` | 所有配置阶段标为 `static` 的扩展的对象文件 |
| `$(STATIC_EXT_LIBS)` | 静态扩展自身依赖的库（libmagic、openssl 等） |
| `$(LDFLAGS)` | 排除 `MSVCRT`/`kernel32`，绑定 `libucrt`/`libvcruntime`/`onecore` |
| `$(X_CUSTOM_LIBS)` | `libzstd_a.lib`、`cares.lib`、`psl.lib`、`ngtcp2*.lib`、`nghttp3.lib` 等 configure 未自动推导的库 |
| `mt.exe` 手动嵌入 manifest | 替代默认的 `$(_VC_MANIFEST_EMBED_EXE)` 规则 |

**为什么需要：** 默认 `php.exe` 目标的链接行不保证按静态构建的预期拼装 `STATIC_EXT_OBJS` / `STATIC_EXT_LIBS`，也不一定吃自定义 `LDFLAGS` 和 `X_CUSTOM_LIBS`。追加独立 target 后，链接行为完全显式可控。

---

## 9. 编译

```bat
nmake /E /f Makefile x-release-static-php
```

| 参数 | 作用 |
|---|---|
| `/E` | 环境变量覆盖 `Makefile` 内宏定义，确保 `CFLAGS` / `LDFLAGS` / `X_CUSTOM_LIBS` 生效 |
| `/f Makefile` | 显式指定 Makefile |
| `x-release-static-php` | 追加的自定义链接目标，显式拼装 `STATIC_EXT_OBJS` / `STATIC_EXT_LIBS` / `LDFLAGS` / `X_CUSTOM_LIBS` |

---

## 10. 验证

```bat
php -m | findstr /I fileinfo
dir /s /b php_fileinfo.dll
dumpbin /dependents .\x64\Release_TS\php.exe
```

**成功标准：**

1. `php -m` 列出 `fileinfo` 及所有目标扩展，且 `php.ini` 中**没有** `extension=`
2. 找不到 `php_fileinfo.dll`（无残留）
3. `dumpbin` 依赖**只有**系统 DLL，**无** `php_*.dll`，**无** `VCRUNTIME140.dll`

**成功样本：**

```
PHP 8.4.26 (cli) (built: Nov  9 2026 16:04:01) (ZTS Visual C++ 2022 x64)
[PHP Modules]
bcmath bz2 Core ctype curl date dom exif fileinfo filter hash iconv json
libxml mbstring openssl pcre PDO Phar random Reflection session SimpleXML
sockets SPL standard tokenizer xml xmlreader xmlwriter zlib
```

```
Dump of file .\x64\Release_TS\php.exe
Image has the following dependencies:
  CRYPT32.dll WLDAP32.dll Normaliz.dll WS2_32.dll IPHLPAPI.DLL
  KERNEL32.dll ole32.dll USER32.dll ADVAPI32.dll SHELL32.dll
  DNSAPI.dll bcrypt.dll
```

---

## 四层模型

| 层 | 解决什么 | 关键修改 |
|---|---|---|
| 构建配置层 | 扩展进不进 `php.exe` | `config.w32` 第三参数 → `false`；`force_all_shared` 失效；`buildconf.bat -f` |
| 源码兼容层 | 默认为动态加载写的代码在静态下会炸 | `winutil.c`、`isatty`、`ZEND_DLIMPORT`、`ZEND_TSRMLS_CACHE_DEFINE`、`HAVE_LIBXML`、`bison` |
| 工具链层 | 编译期/链接期选对 CRT 和系统库 | `/MT /UCRT` + `LDFLAGS` 排除 `MSVCRT`/`kernel32`，绑定 `libucrt`/`libvcruntime`/`onecore` |
| 链接编排层 | Makefile 仍按 DLL 模型拼链接 | 去除 `/dll`、`/nodefaultlib:libcmt`、`/LD /MD`、`_USRDLL`；追加 `x-release-static-php` target；`nmake /E` |

## 常见坑

1. **`buildconf.bat` 顺序**：改 `config.w32` / `confutils.js` 必须在 `buildconf.bat -f` **之前**；直接改 `configure.js` 会被重新生成覆盖。
2. **`config.w32` 参数类型**：必须是布尔 `false`，不是字符串 `"false"`，也不是 `null`。
3. **`null` 的陷阱**：`null` 会走 `shared = PHP_XXX_SHARED` 分支，结果取决于配置阶段变量，不确定。
4. **`sed` 的 `!!`**：cmd 下若启用 `setlocal enabledelayedexpansion`，`!!` 会被吃掉，需改用 `[!][!]` 或 `setlocal disabledelayedexpansion`。
5. **`--enable-all-shared`**：全局开关优先级高于单个 `config.w32`，需同时禁用 `force_all_shared()` 或不传入该参数。
6. **`nmake` 不带 `/E`**：`Makefile` 内可能残留 `/MD` 或默认库宏，会覆盖你 `set` 的 `CFLAGS` / `LDFLAGS`。症状是 `set CFLAGS=/MT` 后直接 `nmake`，`dumpbin` 里仍有 `VCRUNTIME140.dll`。必须 `nmake /E /f Makefile x-release-static-php`。
7. **`PHP_LDFLAGS` 里的 `/nodefaultlib:libcmt`**：这是“PHP 主体当 DLL”模型的产物，会明确排除静态 CRT，抹掉 `/MT` 成果。必须 sed 掉，否则链接回退到动态 CRT。
8. **`.bak` 幂等**：`sed -i.bak` 重复执行是安全的（同一替换多次等于一次），但 `.bak` 不是原始文件，而是上一次修改前的文件。
9. **cmd 单引号**：`sed.exe 's/.../.../'` 在 cmd 下不生效，单引号会被原样传给 sed。sh 脚本里用单引号没问题，`.bat` 里要改双引号。

## 产物

- `php.exe`：单文件，无外部 DLL 依赖（除系统 DLL）
- 无需 `VC++ Redistributable`
- 可直接分发

## 仓库

- 当前开发分支：`https://github.com/jingjingxyk/swoole-cli/tree/win`
- 目标仓库：`swoole-cli` + `https://github.com/swoole/build-static-php`
```
