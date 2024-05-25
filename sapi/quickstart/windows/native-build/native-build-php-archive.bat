@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%\
dir
cd %__PROJECT__%\php-src\x64\Release_TS\
dir
.\php -v
.\php.exe -m
