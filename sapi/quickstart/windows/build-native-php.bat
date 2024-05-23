@echo off

: set LDFLAGS=" -L"C:/Program Files/OpenSSL/lib/" -lssl -lcrypto -lssl -L"C:/Program Files (x86)/zlib/lib" -lz "

: set INCLUDE="%INCLUDE%;c:\php-cli\openssl\include\;c:\php-cli\zlib\include"
: set LIB="%LIB%;c:\php-cli\openssl\lib\;c:\php-cli\zlib\lib"
: set LIBPATH="%LIBPATH%;c:\php-cli\openssl\lib\;c:\php-cli\zlib\lib\"
: echo %INCLUDE%
: echo %LIB%
: echo %LIBPATH%

configure ^
--disable-all      --disable-cgi      --enable-cli ^
--enable-sockets    --enable-mbstring  --enable-ctype  --enable-pdo --enable-phar  ^
--enable-filter ^
--enable-xmlreader  --enable-xmlwriter ^
--enable-zlib ^
--with-openssl=static ^
--with-fileinfo=static ^
--with-extra-includes="c:\php-cli\openssl\include\;c:\php-cli\zlib\include" ^
--with-extra-libs="c:\php-cli\openssl\lib\;c:\php-cli\zlib\lib"

: --enable-fileinfo



: --with-curl=static
