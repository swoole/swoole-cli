@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%\php-src\


: set LDFLAGS=" -L"C:/Program Files/OpenSSL/lib/" -lssl -lcrypto -lssl -L"C:/Program Files (x86)/zlib/lib" -lz "

set INCLUDE="%INCLUDE%;%__PROJECT__%\openssl\include\;%__PROJECT__%\zlib\include"
set LIB="%LIB%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib"
set LIBPATH="%LIBPATH%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib\"
: echo %INCLUDE%
: echo %LIB%
: echo %LIBPATH%

configure ^
--disable-all      --disable-cgi      --enable-cli ^
--enable-sockets    --enable-mbstring  --enable-ctype  --enable-pdo --enable-phar  ^
--enable-filter ^
--enable-xmlreader  --enable-xmlwriter ^
--with-zlib=static ^
--with-openssl=static ^
--with-extra-includes="%__PROJECT__%\build\openssl\include\;%__PROJECT__%\build\zlib\include" ^
--with-extra-libs="%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib"

: --enable-fileinfo
: --with-curl=static

nmake


cd %__PROJECT__%\php-src\

.\x64\Release_TS\php.exe -v
.\x64\Release_TS\php.exe -m

cd %__PROJECT__%
