@echo off
:: cygwin site: https://cygwin.com/
:: start https://cygwin.com/setup-x86_64.exe

setlocal


echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-cygwin-build-deps\

cd /d %__PROJECT__%\var\windows-cygwin-build-deps\

curl.exe -fSLo setup-x86_64.exe https://cygwin.com/setup-x86_64.exe

copy .\setup-x86_64.exe %__PROJECT__%\

endlocal
