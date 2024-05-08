@echo off
echo %~dp0

cd php-sdk-binary-tools
phpsdk-vs17-x64.bat

cd php-src

buildconf
configure --help
configure --disable-all --enable-cli --enable-static=yes --enable-shared=no
nmake
