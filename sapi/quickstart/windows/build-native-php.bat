@echo off

: set LDFLAGS=" -L"C:/Program Files/OpenSSL/lib/" -lssl -lcrypto -lssl -L"C:/Program Files (x86)/zlib/lib" -lz "

set INCLUDE="%INCLUDE%;C:\Program Files\OpenSSL\include\;C:\Program Files (x86)\zlib\include"
set LIB="%LIB%;C:\Program Files\OpenSSL\lib\;C:\Program Files (x86)\zlib\lib"
echo %INCLUDE%
echo %LIB%

configure ^
--disable-all      --disable-cgi      --enable-cli ^
--enable-sockets    --enable-mbstring  --enable-ctype  --enable-pdo --enable-phar  ^
--enable-fileinfo   --enable-filter ^
--enable-xmlreader  --enable-xmlwriter ^
--enable-zlib ^
--with-openssl ^
--with-curl

: --with-extra-includes="'C:\Program Files\OpenSSL\include\':'C:\Program Files (x86)\zlib\include/'" ^
: --with-extra-libs="'C:\Program Files\OpenSSL\lib\':'C:\Program Files (x86)\zlib\lib/'"
