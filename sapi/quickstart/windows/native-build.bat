@echo off
echo %~dp0


cd php-src
dir
buildconf
configure --help
configure --disable-all --enable-cli --enable-static=yes --enable-shared=no
nmake
