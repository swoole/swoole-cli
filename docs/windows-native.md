# Windows 原生 MSVC 静态编译 PHP

本文档记录在 Windows 上使用 MSVC + php-sdk-binary-tools 原生静态编译 PHP 8.4 的完整流程，产物为单文件 `php.exe`，无 `php_*.dll`、无 `VCRUNTIME140.dll` 依赖，可直接分发。

## 1. 环境要求

| 组件 | 版本/来源                                                                        |
|---|------------------------------------------------------------------------------|
| PHP 源码 | 8.4.x，来自 `github.com/php/php-src` 或 `swoole-cli` 内置源码树                       |
| Visual Studio | 2022（含 C++ 桌面开发工作负载）                                                         |
| php-sdk-binary-tools | `github.com/php/php-sdk-binary-tools`，提供 `bison` / `re2c` / `sed` 和 VS 环境初始化 |
| vcpkg | `github.com/microsoft/vcpkg`，`x64-windows-static` 三元组                        |
| Python | 3.10+，用于 meson / ninja（ 构建dav1d libpsl 需要）                                   |

## 2. 入口

所有构建步骤都在 php-sdk 初始化的环境中执行：

```bat
var\native-build\php-sdk-binary-tools\phpsdk-starter.bat ^
    -c vs17 -a x64 ^
    -t sapi\scripts\windows-native\main.bat
```

`phpsdk-starter.bat` 负责初始化 VS 环境、把 php-sdk 的 `bin` 目录（含 `bison` / `re2c` / `sed`）加入 PATH、设置 `PHP_SDK_*` 变量。`main.bat` 内部调用实际构建脚本。

自定义参数通过**环境变量**传递，`-t` 后面不能直接加参数：

```bat
set "PHP_VERSION=8.4.28"
call phpsdk-starter.bat -c vs17 -a x64 -t sapi\scripts\windows-native\config.bat
```

## 3. 依赖库构建

### 3.1 vcpkg 管理（推荐）

```bat

vcpkg install gettext-libintl:x64-windows-static
vcpkg install mpir:x64-windows-static
vcpkg install libpq:x64-windows-static
vcpkg install sqlite3:x64-windows-static
vcpkg install libxslt:x64-windows-static
```

产物统一在 `%VCPKG_ROOT%\installed\x64-windows-static\{include,lib}`。

### 3.2 手动构建

以下库因 vcpkg 端口不支持或需要特定配置，建议手动构建：

| 库 | 构建方式 | 关键参数 |
|---|---|---|
| OpenSSL | CMake | `-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded` |
| zlib | CMake | 同上 |
| curl | CMake | `-DCURL_STATIC_CRT=ON -DCURL_USE_OPENSSL=ON` |
| libxml2 | CMake | `/MT` + `LIBXML_STATIC` |
| libiconv | CMake | `/MT` |
| libpsl | vcpkg 或 nmake | `PSL_STATIC` |
| nghttp2 / nghttp3 / ngtcp2 | CMake | `/MT` + `NGHTTP2_STATICLIB` |
| oniguruma | CMake | `/MT` |
| ICU（intl 依赖） | Cygwin + MSVC 或 MSBuild fork | `--with-data-packaging=static` |
| WinEditLine（readline 依赖） | CMake | `-DMSVC_USE_STATIC_RUNTIME=ON` |

所有依赖库必须用 `/MT` 编译，否则链接时 `LNK2038: mismatch detected for 'RuntimeLibrary'`。

## 4. PHP 源码准备

### 4.1 构建配置层修改（`buildconf.bat -f` 之前）

**强制扩展静态编译**：将 `ext/*/config.w32` 中 `EXTENSION()` 的第三个参数从 `true` 改为布尔 `false`：

```bat
sed.exe -i "s/, true,/, false,/" ext\fileinfo\config.w32
sed.exe -i "s/, true,/, false,/" ext\sqlite3\config.w32
sed.exe -i "s/, true,/, false,/" ext\pdo_sqlite\config.w32
sed.exe -i "s/, true,/, false,/" ext\gd\config.w32
```

> **注意**：必须是布尔 `false`，不是字符串 `"false"`，也不是 `null`。`null` 会回退到 `PHP_XXX_SHARED` 或 `force_all_shared()`，结果不确定。

**禁用全局强制 shared**：修改 `win32/build/confutils.js`：

```bat
sed.exe -i "s/return [!][!]PHP_ALL_SHARED.*/return false;/" win32\build\confutils.js
```

**重新生成 `configure.js`**：

```bat
call buildconf.bat -f
```

### 4.2 源码适配补丁（`buildconf.bat` 之后、`configure.bat` 之前）

```bat
rem 1. 禁用 vcruntime140.dll 运行时版本检查
if not exist "win32\winutil.c.bak" (
    sed.exe -i".bak" "s/#if PHP_LINKER_MAJOR == 14/#if 0/" win32\winutil.c
)

rem 2. POSIX isatty → MSVC CRT 的 _isatty
set "CFLAGS=%CFLAGS% -D isatty=_isatty"
sed.exe -i.bak "s/ZEND_DLIMPORT/ /" Zend\zend_stream.c

rem 3. ZTS 静态 CLI 移除独立 TSRMLS 缓存
sed.exe -i.bak "s/ZEND_TSRMLS_CACHE_DEFINE()/ /" sapi/cli/php_cli.c

rem 4. 禁用 dllmain.c 中 libxml 的 DLL 生命周期钩子
sed.exe -i.bak "s/#ifdef HAVE_LIBXML/#ifdef HAVE_LIBXML_X/" win32/dllmain.c

rem 5. 重新生成 parser
bison.exe -Wall --output=Zend/zend_language_parser.c -v -d Zend/zend_language_parser.y
```

### 4.3 裁剪场景的额外处理（swoole-cli 等）

如果源码树裁剪了 `sapi/phpdbg/`，需从模板中删除对应依赖：

```bat
sed.exe -i.bak "/sapi\\\\phpdbg\\\\phpdbg_parser\.c.*phpdbg_lexer\.c/d" win32\build\Makefile
sed.exe -i.bak "/sapi\\\\phpdbg\\\\phpdbg_parser\.c/d" win32\build\Makefile
```

> 顺序不能反：先删长串（含 `parser.c` + `lexer.c`），再删短串（只含 `parser.c`）。反了会导致 `lexer.c` 残留。

### 4.4 工具链 flag

```bat
set "CFLAGS=/EHsc /MP /MT /UCRT /FS"
set "LDFLAGS=/LTCG /VERBOSE:LIB ^
 /DEFAULTLIB:libvcruntime.lib ^
 /DEFAULTLIB:libucrt.lib ^
 /DEFAULTLIB:onecore.lib ^
 /NODEFAULTLIB:MSVCRT ^
 /NODEFAULTLIB:kernel32.lib"
```

| Flag | 作用 |
|---|---|
| `/MT` | 编译期选定静态 CRT（`LIBCMT.lib`） |
| `/UCRT` | UCRT 静态链接模式下宏路径一致 |
| `/FS` | 序列化 PDB 写入，避免 `jom -j` 并行时文件锁竞争 |
| `/NODEFAULTLIB:MSVCRT` | 切断动态 CRT 回流 |
| `/DEFAULTLIB:libucrt/libvcruntime` | 显式绑定静态 UCRT/VCRuntime |
| `/NODEFAULTLIB:kernel32.lib` + `/DEFAULTLIB:onecore.lib` | 用 OneCore umbrella 收敛 Win32 API 解析 |

## 5. 配置与编译

### 5.1 注入依赖路径

```bat
set "X_INCLUDES=%VCPKG_ROOT%\installed\x64-windows-static\include;%__PROJECT__%\build\openssl\include;..."
set "X_LIBS=%VCPKG_ROOT%\installed\x64-windows-static\lib;%__PROJECT__%\build\openssl\lib;..."

set "INCLUDE=%X_INCLUDES%;%INCLUDE%"
set "LIB=%X_LIBS%;%LIB%"
```

### 5.2 configure 参数

```bat
configure.bat ^
--with-php-build="%__PROJECT__%\build" ^
--with-extra-includes="%INCLUDE%" ^
--with-extra-libs="%LIB%" ^
--with-toolset=vs ^
--with-mp=auto ^
--disable-all ^
--disable-cgi ^
--disable-fpm ^
--disable-phpdbg ^
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
--enable-exif ^
--with-readline ^
--with-sodium ^
--with-gmp ^
--with-pgsql ^
--enable-sqlite3 ^
--enable-odbc ^
--with-pdo-odbc ^
--with-xsl ^
--enable-gd ^
--with-freetype ^
--with-jpeg ^
--with-webp ^
--with-avif
```

> **注意**：如果使用 Swoole 的 `pdo_pgsql` / `pdo_sqlite` Hook（`--enable-swoole-pgsql` / `--enable-swoole-sqlite`），需移除 `--with-pdo-pgsql` / `--with-pdo-sqlite`，两者冲突。

配置成功后，扩展汇总里所有扩展的 Mode 应为 `static`。

### 5.3 去除 Makefile 的 DLL 模型残留

`configure.bat` 之后、`nmake` 之前：

```bat
sed.exe -i "s/DLL_LDFLAGS=\/dll /DLL_LDFLAGS=/" %X_MAKEFILE%
sed.exe -i "s/PHP_LDFLAGS=\$(DLL_LDFLAGS) \/nodefaultlib:libcmt \/def:\$(PHPDEF) /PHP_LDFLAGS=/" %X_MAKEFILE%
sed.exe -i "s/\/LD \/MD/\/MT/" %X_MAKEFILE%
sed.exe -i "s/\/D _USRDLL/ /" %X_MAKEFILE%
```

### 5.4 追加静态链接 target

```bat
echo x-release-static-php^: generated_files $(DEPS_CLI) $(PHP_GLOBAL_OBJS) $(CLI_GLOBAL_OBJS) $(STATIC_EXT_OBJS) $(ASM_OBJS) $(BUILD_DIR)\php.exe.res $(BUILD_DIR)\php.exe.manifest >> %X_MAKEFILE%
echo 	@"$(LINK)" /nologo $(STATIC_EXT_OBJS_RESP) $(PHP_GLOBAL_OBJS_RESP) $(CLI_GLOBAL_OBJS_RESP) $(STATIC_EXT_LIBS) $(ASM_OBJS) $(LIBS) $(LIBS_CLI) $(BUILD_DIR)\php.exe.res /out:$(BUILD_DIR)\php.exe $(LDFLAGS) $(LDFLAGS_CLI) $(X_CUSTOM_LIBS) >> %X_MAKEFILE%
echo 	@if exist php.exe.manifest $(MT) -nologo -manifest php.exe.manifest -outputresource:php.exe >> %X_MAKEFILE%
```

### 5.5 编译

```bat
jom /E /f Makefile x-release-static-php -j %NUMBER_OF_PROCESSORS%
```

`jom` 是 Qt 的并行构建工具，兼容 `nmake` 语法。`/E` 让环境变量覆盖 Makefile 内宏定义。

## 6. 验证

### 6.1 静态性

```bat
dir /s /b php_*.dll                                    rem 应无输出
dumpbin /dependents php.exe | findstr /I "VCRUNTIME MSVCP php_"   rem 应无输出
```

### 6.2 扩展清单

```bat
php -m
```

### 6.3 运行时功能

```bat
rem intl
php -r "echo (new NumberFormatter('en_US', NumberFormatter::DECIMAL))->format(1234567.89);"
rem 预期 1,234,567.89

rem readline
php --ri readline
rem 预期 Readline library => WinEditLine

rem swoole
php -r "Swoole\Coroutine\run(function() { var_dump(Swoole\Coroutine::getCid()); });"
rem 预期 int(1)

rem swoole c-ares
php -r "Swoole\Coroutine\run(function() { var_dump(Swoole\Coroutine::gethostbyname('www.baidu.com')); });"
rem 预期 IP 字符串

rem gd + avif
php -r "$im = imagecreatetruecolor(100,100); imageavif($im, 'out.avif'); var_dump(file_exists('out.avif'));"
php -r "$im = imagecreatefromavif('out.avif'); var_dump($im !== false);"
```

## 7. 常见问题

### 7.1 `U1073: don't know how to make 'sapi\phpdbg\phpdbg_parser.y'`

源码树裁剪了 `sapi/phpdbg/`，但 `win32/build/Makefile` 模板残留依赖。按 4.3 节删除依赖，或补回 `.y`/`.l` 文件。

### 7.2 `LNK2001: unresolved external symbol hook_plain_stream_seek`

`swoole-cli` 的 phar 补丁引入了 `sapi/cli/sfx/hook_stream.h`，但对应 `.c` 未编入。在 `sapi/cli/config.w32` 中补 `ADD_SOURCES`，或 sed `Makefile`。

### 7.3 `LNK2001: unresolved external symbol php_swoole_pgsql_minit`

Swoole 的 PostgreSQL/SQLite Hook 已启用，但 `libpq.lib`/`sqlite3.lib` 未链接。在 `X_CUSTOM_LIBS` 补 `libpq.lib sqlite3.lib`，并移除冲突的 `pdo_pgsql`/`pdo_sqlite`。

### 7.4 `LNK2038: mismatch detected for 'RuntimeLibrary'`

依赖库用 `/MD` 编译，PHP 用 `/MT`。所有依赖必须用 `/MT` 重新构建，vcpkg 用 `x64-windows-static` 三元组。

### 7.5 `.sbr: Permission denied`（`jom -j` 并行时）

`CFLAGS_BD_*` 里的 `/FR` 让多个 `cl.exe` 同时写 `.sbr`。去掉 `/FR`，或加 `/FS`。

### 7.6 `nmake` 不带 `/E` 导致 `/MT` 不生效

`Makefile` 内残留宏定义覆盖环境变量。必须 `jom /E` 或 `nmake /E`。

### 7.7 cmd 下 `sed` 的 `!!` 被吃掉

`setlocal enabledelayedexpansion` 会展开 `!!`。用 `setlocal disabledelayedexpansion` 包裹，或改用 `[!][!]`。

## 8. 与 Cygwin/MSYS2 构建的差异

| 维度 | Cygwin/MSYS2 | 原生 MSVC |
|---|---|---|
| 构建系统 | Autotools + make | configure.js + nmake/jom |
| 编译器 | GCC | MSVC cl.exe |
| POSIX 层 | Cygwin DLL / MSYS2 runtime | 无，全靠 Windows API 适配 |
| 静态 CRT | GCC 默认 `-static-libgcc` | 手动 `/MT` + `LDFLAGS` 排除动态 CRT |
| phpdbg | 完整构建 | 裁剪，模板需清理依赖 |
| `isatty` | POSIX 层提供 | `-D isatty=_isatty` |
| 路径分隔符 | `/` | `\`（Makefile 内） |

原生 MSVC 构建的每个坑，本质都是“POSIX 假设在 MSVC 下的暴露”。Cygwin/MSYS2 时代这些坑被 POSIX 层盖住了。

## 9. 参考

- php-src: `github.com/php/php-src`
- php-sdk-binary-tools: `github.com/php/php-sdk-binary-tools`
- 踩坑记录: `docs/windows-static-php-pitfalls.md`
- swoole-cli Windows 适配 PR: `github.com/swoole/swoole-cli/pull/1095`
- c-ares `ares_socket_t` 修复: `github.com/swoole/swoole-src/commit/8a6389235b30c52f42cf3c0bbacac65ed79bcfa1`
