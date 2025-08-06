@echo off

:: cygwin site: https://cygwin.com/
:: start https://cygwin.com/setup-x86_64.exe

setlocal enableextensions enabledelayedexpansion

echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
cd /d %__PROJECT__%\
echo %cd%

msys2-x86_64.exe

endlocal
