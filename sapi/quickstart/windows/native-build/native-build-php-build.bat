@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%\php-src\



set "INCLUDE=%INCLUDE%;%__PROJECT__%\build\openssl\include\;%__PROJECT__%\build\zlib\include"
set "LIB=%LIB%;%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib"
set "LIBPATH=%LIBPATH%;%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib\"

echo %INCLUDE%
echo %LIB%
echo %LIBPATH%

rem set "CFLAGS=/EHsc /MT "
rem set "LDFLAGS=/WHOLEARCHIVE /FORCE:MULTIPLE"

configure.bat ^
--disable-all         --disable-cgi      --enable-cli   ^
--enable-sockets      --enable-ctype     --enable-pdo    --enable-phar  ^
--enable-filter ^
--enable-xmlreader   --enable-xmlwriter ^
--enable-tokenizer ^
--disable-zts ^
--enable-apcu ^
--enable-bcmath ^
--enable-zlib  ^
--with-openssl=static ^
--with-toolset=vs ^
--with-extra-includes="%INCLUDE%" ^
--with-extra-libs="%LIB%"


:: --enable-mbstring
:: --enable-redis ^
:: --enable-phar-native-ssl
:: --enable-fileinfo
:: --with-curl=static

cd /d %__PROJECT__%

set __PROJECT__=
endlocal
