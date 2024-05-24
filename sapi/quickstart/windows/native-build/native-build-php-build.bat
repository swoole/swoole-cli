@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%\php-src\



set "INCLUDE=%INCLUDE%;%__PROJECT__%\build\openssl\include\;%__PROJECT__%\build\zlib\include"
set "LIB=%LIB%;%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib"
set "LIBPATH=%LIBPATH%;%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib\"

echo %INCLUDE%
echo %LIB%
echo %LIBPATH%

set "LIBPATH=%LIBPATH%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib"


configure.bat ^
--disable-all      --disable-cgi      --enable-cli ^
--enable-sockets      --enable-ctype  --enable-pdo --enable-phar  ^
--enable-filter ^
--enable-xmlreader  --enable-xmlwriter ^
--enable-zlib  ^
--with-openssl=static ^
--enable-tokenizer ^
--with-toolset=vs ^
--with-extra-includes="%__PROJECT__%\build\openssl\include\;%__PROJECT__%\build\zlib\include" ^
--with-extra-libs="%__PROJECT__%\build\openssl\lib\;%__PROJECT__%\build\zlib\lib\"

:: --enable-mbstring
:: --enable-redis ^
:: --enable-phar-native-ssl
:: --enable-fileinfo
:: --with-curl=static

cd %__PROJECT__%
