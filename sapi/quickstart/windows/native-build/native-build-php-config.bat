@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%\php-src
echo %cd%

buildconf.bat -f

cd %__PROJECT__%

endlocal
