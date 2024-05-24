@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%\php-src\
dir

x64\Release_TS\php.exe -v
x64\Release_TS\php.exe -m
