@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
:: cd /d %__PROJECT__%\var\native-build\php-src\
:: nmake clean

cd /d %__PROJECT__%\var\native-build
if exist "php-src" rmdir /s /q php-src
dir
git clone -b php-8.4.26 --depth=1 https://github.com/php/php-src.git php-src
endlocal
