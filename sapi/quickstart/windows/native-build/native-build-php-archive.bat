@echo off

echo %~dp0
cd %~dp0
cd /d  ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%\
dir

rem cd %__PROJECT__%\php-src\x64\Release_TS\
cd /d %__PROJECT__%\php-src\x64\Release\
dir

.\php -v
.\php -m
