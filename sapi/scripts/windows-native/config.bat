@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "PATH=%__PROJECT__%\var\native-build\php-sdk-binary-tools\bin\;%__PROJECT__%\var\native-build\php-sdk-binary-tools\msys2\bin;%PATH%;"

vswhere -products * -latest -prerelease -find **\VC\Auxiliary\Build\vcvarsall.bat
rem call "C:\Program Files\Microsoft Visual Studio\2022\Enterprise\VC\Auxiliary\Build\vcvars64.bat"
env


where bison
bison --version
re2c --version
set "SDK_UM=%WindowsSdkDir%Include\%WindowsSDKVersion%um"
echo "%SDK_UM%"

set "__PROJECT__=%cd%"
echo %cd%
cd /d %__PROJECT__%
set "PHP_SRC=%cd%"
echo %cd%

if exist "configure.js" (
::    nmake clean
    del /Q "configure.js"
)


set "X_CFLAGS="
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

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\icu\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\icu\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\gettext\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\gettext\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libsodium\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libsodium\lib"

:: set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libedit\include"
:: set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libedit\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\wineditline\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\wineditline\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\sqlite3\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\sqlite3\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\pgsql\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\pgsql\lib"

rem with mpir replace gmp on windows
set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\mpir\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\mpir\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libxslt\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libxslt\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libpng\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libpng\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\freetype\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\freetype\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libjpeg\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libjpeg\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libgif\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libgif\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libwebp\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libwebp\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\dav1d\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\dav1d\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\svt_av1\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\svt_av1\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libyuv\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libyuv\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\libavif\include"
set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\libavif\lib"

set "X_INCLUDES=%X_INCLUDES%;%__PROJECT__%\build\include"

set "X_INCLUDES=%X_INCLUDES%;%SDK_UM%"

set "X_LIBS=%X_LIBS%;%__PROJECT__%\build\lib"

:: set "INCLUDE=%INCLUDE%;%PHP_SRC%\ext"
set "INCLUDE=%X_INCLUDES%;%INCLUDE%"
set "LIB=%X_LIBS%;%LIB%"

echo "INCLUDE=%INCLUDE%"
echo "LIB=%LIB%"

set "PHP_PHP_BUILD=%__PROJECT__%\build"
echo %PHP_PHP_BUILD%
mkdir %PHP_PHP_BUILD%

:: fileinfo extension patch
sed.exe -i.bak "s/, true,/, false,/" ext\fileinfo\config.w32
sed.exe -i.bak "s/!!PHP_ALL_SHARED && \"yes\" == PHP_ALL_SHARED/false/" win32\build\confutils.js

:: intl  extension patch
sed.exe -i.bak "s/, true,/, false,/" ext\intl\config.w32
:: gd  extension patch
sed.exe -i.bak "s/gd2copypal\.c //" ext/gd/config.w32
:: swoole extension patch
sed.exe -i.bak "s/PHP_CARES_DIR/null/" ext/swoole/config.w32
sed.exe -i.bak "s/PHP_PGSQL_DIR/null/" ext/swoole/config.w32

call buildconf.bat -f

echo "========HELP============"

call configure.bat --help


:: sed.exe -i.bak '/var dir_part_to_add = "";/i  STDOUT.WriteLine(header_name);' configure.js
:: sed.exe -i.bak '/dep_present = eval("PHP_" + DEP);/i  STDOUT.WriteLine(extname);' configure.js

:: sed.exe -i.bak '/if (absolute_path.indexOf(PHP_PHP_BUILD) == 0) {/i  STDOUT.WriteLine(path);' configure.js


:: sed.exe -i.bak '/var i, j, k, libname;/i STDOUT.WriteLine(libnames);' configure.js
:: sed.exe -i.bak '/var i, j, k, libname;/i STDOUT.WriteLine(target);' configure.js
:: sed.exe -i.bak '/var i, j, k, libname;/i STDOUT.WriteLine(path_to_check);' configure.js
:: sed.exe -i.bak '/var i, j, k, libname;/i STDOUT.WriteLine(common_name);' configure.js

:: sed.exe -i.bak "/var objs = null;/i STDOUT.WriteLine(extname + \" \" + shared);" configure.js
:: sed.exe -i.bak "/var objs = null;/i STDOUT.WriteLine(force_all_shared());" configure.js

sed.exe -i.bak '/generate_files();/i STDOUT.WriteLine("=======================");' configure.js
sed.exe -i.bak '/generate_files();/i STDOUT.WriteLine(PHP_PHP_BUILD);' configure.js

echo "===================="


set "X_CFLAGS=%X_CFLAGS% /EHsc /MP /MT /UCRT"
set "X_CFLAGS=%X_CFLAGS% /FS"
:: for icu static link
set "X_CFLAGS=%X_CFLAGS% -DU_STATIC_IMPLEMENTATION"

set "X_CFLAGS=%X_CFLAGS% -DSODIUM_STATIC=1"
set "X_CFLAGS=%X_CFLAGS% -DCARES_STATICLIB"
set "X_CFLAGS=%X_CFLAGS% -DLIBPQ_STATIC"

set "CFLAGS=%X_CFLAGS%"


:: /showIncludes
:: /MANIFEST /MANIFESTUAC:"level='asInvoker' uiAccess='false'" /manifest:embed

rem https://learn.microsoft.com/zh-cn/cpp/c-runtime-library/crt-library-features?view=msvc-170

set "LDFLAGS="
:: set "LDFLAGS=%LDFLAGS% /VERBOSE"
set "LDFLAGS=%LDFLAGS% /VERBOSE:LIB"
set "LDFLAGS=%LDFLAGS% /MAP:php.map"
set "LDFLAGS=%LDFLAGS% /LTCG"
set "LDFLAGS=%LDFLAGS% /DEFAULTLIB:libvcruntime.lib"
set "LDFLAGS=%LDFLAGS% /DEFAULTLIB:libucrt.lib"
set "LDFLAGS=%LDFLAGS% /NODEFAULTLIB:MSVCRT"



:: set "LDFLAGS=%LDFLAGS% kernel32.lib ole32.lib user32.lib advapi32.lib shell32.lib ws2_32.lib Dnsapi.lib psapi.lib bcrypt.lib"
:: dir "%LIBS%" | findstr ws2_32.lib
:: echo %LIBPATH%

rem no link vcruntime140d.dll
rem https://github.com/php/php-src/blob/PHP-8.5.8/win32/winutil.c#L487

if not exist "win32\winutil.c.bak" (
    echo "win32\winutil.c.bak no found , now creating it ..."
    sed.exe -i.bak "s/#if PHP_LINKER_MAJOR == 14/#if 0/" win32\winutil.c
)


set "CFLAGS=%CFLAGS% -D isatty=_isatty"

sed.exe -i.bak 's/ZEND_DLIMPORT/ /' Zend\zend_stream.c

sed.exe -i.bak 's/ZEND_TSRMLS_CACHE_DEFINE()/ /' sapi/cli/php_cli.c

sed.exe -i.bak 's/#ifdef HAVE_LIBXML/#ifdef HAVE_LIBXML_X/' win32/dllmain.c

:: bison.exe -Wall --output=Zend/zend_language_parser.c -v -d Zend/zend_language_parser.y

sed.exe -i.bak "s/sapi\\phpdbg\\phpdbg_parser\.c//" Makefile
sed.exe -i.bak "s/sapi\\phpdbg\\phpdbg_parser\.c sapi\\phpdbg\\phpdbg_lexer\.c//" Makefile

set CL=/MP


configure.bat ^
--with-php-build="%PHP_PHP_BUILD%" ^
--with-extra-includes="%INCLUDE%" ^
--with-extra-libs="%LIB%" ^
--with-toolset=vs ^
--with-mp=auto ^
--disable-all  ^
--disable-phpdbg ^
--disable-cgi  ^
--enable-cli   ^
--enable-zts ^
--enable-filter ^
--enable-session ^
--enable-tokenizer ^
--enable-ctype    ^
--enable-pdo  ^
--enable-phar  ^
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
--enable-swoole ^
--enable-cares ^
--with-nghttp2-dir="%__PROJECT__%\build\libnghttp2" ^
--enable-php-sockets ^
--enable-mysqlnd ^
--enable-swoole-curl ^
--enable-swoole-thread ^
--with-gd ^
--with-libwebp ^
--with-libavif ^
--enable-intl ^
--enable-redis ^
--with-gettext ^
--with-mysqlnd ^
--with-mysqli ^
--with-pdo-mysql=mysqlnd ^
--enable-soap ^
--with-sodium ^
--with-readline ^
--with-sqlite3 ^
--with-pgsql ^
--with-gmp ^
--with-xsl ^
--enable-odbc ^
--with-odbcver ^




goto :skip_code



:: --enable-thread-context

:: --with-ldap
:: --enable-cli-win32 ^
:: --disable-zts ^
:: --enable-apcu ^

:: need fix swoole config.w32

--enable-swoole-sqlite ^
--enable-swoole-pgsql ^
--enable-swoole-ftp ^
--with-swoole-ssh2 ^
--with-swoole-odbc ^
--with-pdo-sqlite ^
--with-pdo-pgsql  ^
--with-pdo-odbc


:skip_code
echo 'continue'

cd /d %__PROJECT__%
endlocal
