@echo off
echo %~dp0

cd php-sdk-binary-tools
dir
phpsdk-vs17-x64.bat
cd ..

cd php-src
dir
buildconf
configure --help
configure --disable-all --enable-cli --enable-static=yes --enable-shared=no
nmake
