@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%


cd php-src
start /B buildconf
start /B  configure --help



cd %__PROJECT__%
